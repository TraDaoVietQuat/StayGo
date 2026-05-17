<?php

namespace App\Http\Controllers;

use App\Mail\AdminBookingCancelled;
use App\Mail\AdminNewBooking;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\Promo;
use App\Models\Room;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingCancelledPartnerNotification;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\NewBookingPartnerNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Room $room, Request $request)
    {
        $room->load('hotel.location');

        $checkin   = $request->input('checkin');
        $checkout  = $request->input('checkout');
        $guests    = (int) $request->input('guests', 1);
        $stayType  = $request->input('stay_type', 'night'); // night | day

        // Kiểm tra phòng trống nếu có ngày
        $availableCount = ($checkin && $checkout)
            ? $room->availableCount($checkin, $checkout)
            : $room->quantity;

        // Kiểm tra ưu đãi QR có trong session không
        $hasPromo = false;
        if (
            Auth::check() &&
            session('promo_new_user') === 'NEWUSER10' &&
            Booking::where('user_id', Auth::id())->whereNotIn('status', ['cancelled'])->count() === 0
        ) {
            $hasPromo = true;
        }

        // Giá hiển thị theo loại ở
        $displayPrice = ($stayType === 'day' && $room->day_price)
            ? $room->day_price
            : $room->price;

        return view('pages.booking', compact(
            'room', 'checkin', 'checkout', 'guests',
            'availableCount', 'hasPromo', 'stayType', 'displayPrice'
        ));
    }

    public function store(Request $request)
    {
        // Honeypot: bots fill hidden fields, real users don't
        if ($request->filled('_hp_name') || $request->filled('_hp_email')) {
            return back()->withErrors(['check_in' => 'Yêu cầu không hợp lệ.'])->withInput();
        }

        $request->validate([
            'room_id'        => ['required', 'exists:rooms,id'],
            'full_name'      => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email:rfc,dns'],
            'phone'          => ['required', 'string', 'regex:/^[0-9\s\+\-\(\)]{8,20}$/'],
            'check_in'       => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out'      => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'payment_method' => ['required', 'in:bank,momo,vnpay,card,hotel,zalopay,cod,bank_transfer'],
        ], [
            'full_name.required'      => 'Vui lòng nhập họ tên.',
            'email.required'          => 'Vui lòng nhập email.',
            'phone.required'          => 'Vui lòng nhập số điện thoại.',
            'check_in.required'       => 'Vui lòng chọn ngày nhận phòng.',
            'check_in.after_or_equal' => 'Ngày nhận phòng phải từ hôm nay.',
            'check_out.after'         => 'Ngày trả phòng phải sau ngày nhận phòng.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ]);

        $checkIn  = $request->input('check_in');
        $checkOut = $request->input('check_out');
        $stayType = $request->input('stay_type', 'night');

        // Ngăn cùng 1 user đặt trùng phòng + ngày
        if (Auth::check()) {
            $duplicate = Booking::where('user_id', Auth::id())
                ->where('room_id', $request->input('room_id'))
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('check_in', '<', $checkOut)
                ->where('check_out', '>', $checkIn)
                ->exists();
            if ($duplicate) {
                return back()->withErrors(['check_in' => 'Bạn đã có đặt phòng này trong khoảng thời gian đó rồi.'])->withInput();
            }
        }

        // ---- GeoIP: detect quốc gia từ IP (non-blocking, timeout 3s) ----
        $this->resolveGeoIp($request);

        // ---- Redis distributed lock — ngăn 2 request cùng đặt 1 phòng 1 lúc ----
        $roomId    = $request->input('room_id');
        $lockKey   = "booking_lock:room_{$roomId}:{$checkIn}:{$checkOut}";
        $lock      = Cache::lock($lockKey, 10); // giữ tối đa 10 giây

        if (! $lock->get()) {
            return back()->withErrors(['check_in' => 'Hệ thống đang xử lý đặt phòng này, vui lòng thử lại.'])->withInput();
        }

        // ---- Tạo booking bên trong transaction + DB lock ----
        try {
            $booking = DB::transaction(function () use ($request, $checkIn, $checkOut, $stayType) {
            $room = Room::findOrFail($request->input('room_id'));

            // Lock row để tránh race condition overbooking
            if (!$room->lockAndCheckAvailability($checkIn, $checkOut)) {
                throw new \RuntimeException('ROOM_FULL');
            }

            $nights    = \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut);
            $unitPrice = ($stayType === 'day' && $room->day_price) ? $room->day_price : $room->price;
            $baseTotal = $unitPrice * max($nights, 1);
            $orderCode = 'SG' . strtoupper(Str::random(8));

            // ---- Kiểm tra ưu đãi ----
            $discountCode    = null;
            $discountPercent = 0;
            $discountAmount  = 0;
            $total           = $baseTotal;

            // Ưu tiên 1: QR promo (NEWUSER10 session)
            if (
                Auth::check() &&
                session('promo_new_user') === 'NEWUSER10' &&
                Booking::where('user_id', Auth::id())->whereNotIn('status', ['cancelled'])->count() === 0
            ) {
                $discountPercent = 10;
                $discountAmount  = round($baseTotal * 0.1, 2);
                $total           = $baseTotal - $discountAmount;
                $discountCode    = 'NEWUSER10';
                session()->forget('promo_new_user');
                session()->forget('promo_new_user_at');
            }
            // Ưu tiên 2: mã giảm giá nhập tay
            elseif ($request->filled('promo_code') && $request->input('promo_applied') === '1') {
                $promoModel = Promo::where('code', strtoupper($request->input('promo_code')))->first();
                if ($promoModel && $promoModel->isValid($baseTotal, Auth::user())) {
                    $discountAmount  = $promoModel->calculateDiscount($baseTotal);
                    $discountPercent = $promoModel->type === 'percent' ? (int) $promoModel->value : 0;
                    $total           = $baseTotal - $discountAmount;
                    $discountCode    = $promoModel->code;
                    $promoModel->incrementUsed();
                }
            }

            return Booking::create([
                'order_code'          => $orderCode,
                'user_id'             => Auth::id(),
                'room_id'             => $room->id,
                'full_name'           => $request->input('full_name'),
                'email'               => $request->input('email'),
                'phone'               => $request->input('phone'),
                'check_in'            => $checkIn,
                'check_out'           => $checkOut,
                'total_price'         => $total,
                'payment_method'      => $request->input('payment_method'),
                'note'                => $request->input('note'),
                'stay_type'           => $stayType,
                'discount_code'       => $discountCode,
                'discount_percent'    => $discountPercent,
                'discount_amount'     => $discountAmount,
                'status'              => 'pending',
                'created_at'          => now(),
                'guest_country'       => $request->attributes->get('geo_country'),
                'guest_country_code'  => $request->attributes->get('geo_country_code'),
                'guest_city'          => $request->attributes->get('geo_city'),
            ]);
            });
        } catch (\RuntimeException $e) {
            $lock->release();
            if ($e->getMessage() === 'ROOM_FULL') {
                return back()->withErrors(['check_in' => 'Phòng đã hết chỗ cho khoảng thời gian này. Vui lòng chọn ngày khác.'])->withInput();
            }
            throw $e;
        } finally {
            $lock->release();
        }

        // Gửi email xác nhận (load relations trước)
        $booking->load('room.hotel');
        try {
            Mail::to($booking->email)->send(new BookingConfirmation($booking));
        } catch (\Exception $e) {}

        // Thông báo cho admin
        try {
            Mail::to(config('mail.from.address'))->send(new AdminNewBooking($booking));
        } catch (\Exception $e) {}

        // Thông báo DB cho user đã đăng nhập
        if (Auth::check()) {
            try {
                Auth::user()->notify(new BookingConfirmedNotification($booking));
            } catch (\Exception $e) {}
        }

        // Thông báo real-time cho hotel partner
        try {
            $partnerUserId = $booking->room->hotel->partner_user_id ?? null;
            if ($partnerUserId) {
                $partner = User::find($partnerUserId);
                $partner?->notify(new NewBookingPartnerNotification($booking));
            }
        } catch (\Exception $e) {}

        session(['booking_order_code' => $booking->order_code]);

        return redirect()->route('payment.show', $booking)
            ->with('success', 'Đặt phòng thành công! Vui lòng hoàn tất thanh toán. Email xác nhận đã được gửi.');
    }

    public function myBookings()
    {
        /** @var \App\Models\User $user */
        $user     = Auth::user();
        $bookings = $user->bookings()
            ->with(['room.hotel'])
            ->latest('created_at')
            ->paginate(10);

        return view('pages.my-bookings', compact('bookings'));
    }

    public function cancel(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(!in_array($booking->status, ['pending', 'confirmed']), 400, 'Không thể hủy đặt phòng này.');

        $booking->update(['status' => 'cancelled']);

        // Thông báo hủy phòng
        try {
            $booking->load('room.hotel', 'payment');
            $booking->user->notify(new BookingCancelledNotification($booking));
            if ($booking->email) {
                Mail::to($booking->email)->send(new BookingCancelled($booking));
            }
            Mail::to(config('mail.from.address'))->send(new AdminBookingCancelled($booking));

            // Thông báo real-time cho hotel partner
            $partnerUserId = $booking->room->hotel->partner_user_id ?? null;
            if ($partnerUserId) {
                $partner = User::find($partnerUserId);
                $partner?->notify(new BookingCancelledPartnerNotification($booking));
            }
        } catch (\Exception) {}

        return back()->with('success', 'Đã hủy đặt phòng thành công.');
    }

    public function requestRefund(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->refund_requested, 400, 'Bạn đã gửi yêu cầu hoàn tiền trước đó.');

        $booking->update([
            'refund_requested'    => true,
            'refund_requested_at' => now(),
            'refund_amount'       => $booking->total_price * 0.8,
        ]);

        return back()->with('success', 'Yêu cầu hoàn tiền đã được ghi nhận. Chúng tôi sẽ xử lý trong 3-5 ngày làm việc.');
    }

    public function invoice(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        $booking->load('room.hotel');
        $service     = app(\App\Services\InvoiceService::class);
        $content     = $service->generate($booking);
        $contentType = $service->contentType();
        $ext         = str_contains($contentType, 'pdf') ? 'pdf' : 'html';
        return response($content, 200, [
            'Content-Type'        => $contentType,
            'Content-Disposition' => "attachment; filename=\"hoa-don-{$booking->order_code}.{$ext}\"",
        ]);
    }

    private function resolveGeoIp(Request $request): void
    {
        $ip = $request->ip();
        if (
            in_array($ip, ['127.0.0.1', '::1']) ||
            str_starts_with($ip, '192.168.') ||
            str_starts_with($ip, '10.') ||
            str_starts_with($ip, '172.')
        ) {
            return;
        }
        try {
            $geo = Http::timeout(3)
                ->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,city&lang=vi")
                ->json();
            if (($geo['status'] ?? '') === 'success') {
                $request->attributes->set('geo_country',      $geo['country']     ?? null);
                $request->attributes->set('geo_country_code', $geo['countryCode'] ?? null);
                $request->attributes->set('geo_city',         $geo['city']        ?? null);
            }
        } catch (\Exception $e) {
            Log::debug('GeoIP lookup failed: ' . $e->getMessage());
        }
    }
}
