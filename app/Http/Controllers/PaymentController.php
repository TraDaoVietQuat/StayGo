<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\MoMoService;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private function canAccess(Booking $booking): bool
    {
        if (Auth::check() && $booking->user_id === Auth::id()) return true;
        if (!$booking->user_id && session('booking_order_code') === $booking->order_code) return true;
        return false;
    }

    public function show(Booking $booking)
    {
        abort_if(!$this->canAccess($booking), 403);
        $booking->load('room.hotel');
        $payment = $booking->payment;
        return view('pages.payment', compact('booking', 'payment'));
    }

    public function status(Booking $booking)
    {
        abort_if(!$this->canAccess($booking), 403);
        $payment = $booking->payment;
        return response()->json([
            'payment_status' => $payment?->payment_status ?? 'pending',
            'booking_status' => $booking->status,
        ]);
    }

    public function process(Request $request, Booking $booking)
    {
        abort_if(!$this->canAccess($booking), 403);

        // Đã có payment completed → không xử lý lại
        if ($booking->payment && $booking->payment->payment_status === 'completed') {
            return redirect()->route('booking.my')->with('info', 'Đơn này đã được thanh toán.');
        }

        $method = $booking->payment_method;

        // --- Thanh toán trực tiếp (hotel / cod) → tạo pending record ---
        if (in_array($method, ['hotel', 'cod', 'bank_transfer', 'bank'])) {
            DB::transaction(function () use ($booking) {
                if (!$booking->payment) {
                    Payment::create([
                        'booking_id'     => $booking->id,
                        'hotel_id'       => $booking->room?->hotel_id,
                        'hotel_name'     => $booking->room?->hotel?->name,
                        'room_name'      => $booking->room?->room_name,
                        'method'         => $booking->payment_method,
                        'full_name'      => $booking->full_name,
                        'email'          => $booking->email,
                        'phone'          => $booking->phone,
                        'amount'         => $booking->total_price,
                        'payment_status' => 'pending',
                    ]);
                }
            });

            return redirect()->route('payment.show', $booking)->with('success', true);
        }

        // --- VNPay ---
        if ($method === 'vnpay') {
            try {
                $vnpay = app(VNPayService::class);
                $url   = $vnpay->createPaymentUrl([
                    'order_code' => $booking->order_code,
                    'amount'     => $booking->total_price,
                    'ip'         => $request->ip(),
                ]);
                return redirect()->away($url);
            } catch (\Exception $e) {
                Log::error('VNPay redirect error: ' . $e->getMessage());
                return back()->with('error', 'Không thể kết nối VNPay. Vui lòng thử lại sau.');
            }
        }

        // --- MoMo ---
        if ($method === 'momo') {
            try {
                $momo     = app(MoMoService::class);
                $response = $momo->createPayment([
                    'order_code' => $booking->order_code,
                    'amount'     => $booking->total_price,
                ]);

                if (!empty($response['payUrl'])) {
                    return redirect()->away($response['payUrl']);
                }

                Log::error('MoMo create payment failed', $response);
                return back()->with('error', 'Không thể tạo yêu cầu MoMo. Vui lòng thử lại.');
            } catch (\Exception $e) {
                Log::error('MoMo error: ' . $e->getMessage());
                return back()->with('error', 'Lỗi kết nối MoMo. Vui lòng thử lại sau.');
            }
        }

        // --- Các phương thức còn lại (zalopay, card…) → pending ---
        DB::transaction(function () use ($booking) {
            if (!$booking->payment) {
                Payment::create([
                    'booking_id'     => $booking->id,
                    'hotel_id'       => $booking->room?->hotel_id,
                    'hotel_name'     => $booking->room?->hotel?->name,
                    'room_name'      => $booking->room?->room_name,
                    'method'         => $booking->payment_method,
                    'full_name'      => $booking->full_name,
                    'email'          => $booking->email,
                    'phone'          => $booking->phone,
                    'amount'         => $booking->total_price,
                    'payment_status' => 'pending',
                ]);
            }
        });

        return redirect()->route('payment.show', $booking)->with('success', true);
    }
}
