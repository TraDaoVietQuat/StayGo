<?php

namespace App\Http\Controllers;

use App\Mail\AdminNewBooking;
use App\Mail\PaymentConfirmed;
use App\Mail\PaymentFailed;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\MoMoService;
use App\Services\VNPayService;
use App\Services\ZalopayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentWebhookController extends Controller
{
    // =========================================================
    // VNPay
    // =========================================================

    /** IPN — VNPay gọi ngầm sau khi thanh toán */
    public function vnpayIpn(Request $request, VNPayService $vnpay)
    {
        $data = $request->all();

        if (!$vnpay->verifyCallback($data)) {
            Log::warning('VNPay IPN: chữ ký không hợp lệ', $data);
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $orderCode = $vnpay->getOrderCode($data);
        $booking   = Booking::where('order_code', $orderCode)->first();

        if (!$booking) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        if ($booking->payment && $booking->payment->payment_status === 'completed') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        DB::transaction(function () use ($booking, $data, $vnpay) {
            $status = $vnpay->isSuccess($data) ? 'completed' : 'failed';
            $txnNo  = $vnpay->getTransactionNo($data);
            $paidAt = $status === 'completed' ? now() : null;

            if ($booking->payment) {
                $booking->payment->update([
                    'payment_status' => $status,
                    'transaction_no' => $txnNo,
                    'paid_at'        => $paidAt,
                ]);
            } else {
                Payment::create([
                    'booking_id'     => $booking->id,
                    'hotel_id'       => $booking->room?->hotel_id,
                    'hotel_name'     => $booking->room?->hotel?->name,
                    'room_name'      => $booking->room?->room_name,
                    'method'         => 'vnpay',
                    'full_name'      => $booking->full_name,
                    'email'          => $booking->email,
                    'phone'          => $booking->phone,
                    'amount'         => $booking->total_price,
                    'payment_status' => $status,
                    'transaction_no' => $txnNo,
                    'paid_at'        => $paidAt,
                ]);
            }

            if ($status === 'completed') {
                $booking->update(['status' => 'confirmed']);
            }
        });

        // Gửi email theo kết quả thanh toán
        if ($booking->email) {
            try {
                $booking->load('room.hotel', 'payment');
                if ($vnpay->isSuccess($data)) {
                    Mail::to($booking->email)->send(new PaymentConfirmed($booking));
                } else {
                    Mail::to($booking->email)->send(new PaymentFailed($booking));
                }
            } catch (\Exception $e) {
                Log::error('Payment email failed (VNPay): ' . $e->getMessage());
            }
        }

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm success']);
    }

    /** Return URL — user được redirect về sau khi trả */
    public function vnpayReturn(Request $request, VNPayService $vnpay)
    {
        $data = $request->all();

        if (!$vnpay->verifyCallback($data)) {
            return redirect()->route('home')->with('error', 'Xác minh thanh toán thất bại.');
        }

        $orderCode = $vnpay->getOrderCode($data);
        $booking   = Booking::where('order_code', $orderCode)->with('room.hotel')->first();

        if (!$booking) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn đặt phòng.');
        }

        if ($vnpay->isSuccess($data)) {
            return redirect()->route('booking.my')
                ->with('success', "Thanh toán thành công! Đặt phòng #{$orderCode} đã được xác nhận.");
        }

        return redirect()->route('payment.show', $booking)
            ->with('error', 'Thanh toán VNPay không thành công. Vui lòng thử lại.');
    }

    // =========================================================
    // MoMo
    // =========================================================

    /** IPN — MoMo gọi ngầm */
    public function momoIpn(Request $request, MoMoService $momo)
    {
        $data = $request->all();

        if (!$momo->verifySignature($data)) {
            Log::warning('MoMo IPN: chữ ký không hợp lệ', $data);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $orderCode = $momo->getOrderCode($data);
        $booking   = Booking::where('order_code', $orderCode)->first();

        if (!$booking) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::transaction(function () use ($booking, $data, $momo) {
            $status = $momo->isSuccess($data) ? 'completed' : 'failed';
            $txnId  = $momo->getTransactionId($data);
            $paidAt = $status === 'completed' ? now() : null;

            if ($booking->payment) {
                $booking->payment->update([
                    'payment_status' => $status,
                    'transaction_no' => $txnId,
                    'paid_at'        => $paidAt,
                ]);
            } else {
                Payment::create([
                    'booking_id'     => $booking->id,
                    'hotel_id'       => $booking->room?->hotel_id,
                    'hotel_name'     => $booking->room?->hotel?->name,
                    'room_name'      => $booking->room?->room_name,
                    'method'         => 'momo',
                    'full_name'      => $booking->full_name,
                    'email'          => $booking->email,
                    'phone'          => $booking->phone,
                    'amount'         => $booking->total_price,
                    'payment_status' => $status,
                    'transaction_no' => $txnId,
                    'paid_at'        => $paidAt,
                ]);
            }

            if ($status === 'completed') {
                $booking->update(['status' => 'confirmed']);
            }
        });

        // Gửi email theo kết quả thanh toán
        if ($booking->email) {
            try {
                $booking->load('room.hotel', 'payment');
                if ($momo->isSuccess($data)) {
                    Mail::to($booking->email)->send(new PaymentConfirmed($booking));
                } else {
                    Mail::to($booking->email)->send(new PaymentFailed($booking));
                }
            } catch (\Exception $e) {
                Log::error('Payment email failed (MoMo): ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'OK']);
    }

    // =========================================================
    // SePay — tự động nhận diện chuyển khoản ngân hàng
    // =========================================================

    public function sepayWebhook(Request $request)
    {
        // Verify webhook token if configured
        $token = config('services.sepay.webhook_token');
        if ($token && $request->header('Authorization') !== 'Apikey ' . $token) {
            Log::warning('SePay webhook: unauthorized request', ['ip' => $request->ip()]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        Log::info('SePay webhook received', $data);

        // Chỉ xử lý giao dịch tiền vào
        if (($data['transferType'] ?? '') !== 'in') {
            return response()->json(['success' => true]);
        }

        // Tìm order code trong nội dung chuyển khoản
        $content = $data['transaction_content'] ?? $data['content'] ?? $data['description'] ?? '';
        $amount  = (int) ($data['transferAmount'] ?? $data['amount'] ?? 0);

        // Tìm booking khớp với order code trong nội dung (có thể có prefix SEVQR)
        $booking = Booking::where('status', 'pending')
            ->get()
            ->first(fn($b) => str_contains(strtoupper($content), strtoupper($b->order_code)));

        if (!$booking) {
            Log::warning('SePay: không tìm được booking từ nội dung: ' . $content);
            return response()->json(['success' => false, 'message' => 'Booking not found']);
        }

        if ($booking->payment && $booking->payment->payment_status === 'completed') {
            return response()->json(['success' => true, 'message' => 'Already completed']);
        }

        DB::transaction(function () use ($booking, $amount, $data) {
            $txnNo = $data['id'] ?? $data['referenceCode'] ?? null;

            if ($booking->payment) {
                $booking->payment->update([
                    'payment_status' => 'completed',
                    'transaction_no' => $txnNo,
                    'paid_at'        => now(),
                ]);
            } else {
                Payment::create([
                    'booking_id'     => $booking->id,
                    'hotel_id'       => $booking->room?->hotel_id,
                    'hotel_name'     => $booking->room?->hotel?->name,
                    'room_name'      => $booking->room?->room_name,
                    'method'         => 'bank_transfer',
                    'full_name'      => $booking->full_name,
                    'email'          => $booking->email,
                    'phone'          => $booking->phone,
                    'amount'         => $amount ?: $booking->total_price,
                    'payment_status' => 'completed',
                    'transaction_no' => $txnNo,
                    'paid_at'        => now(),
                ]);
            }

            $booking->update(['status' => 'confirmed']);
        });

        Log::info('SePay: xác nhận thanh toán booking ' . $booking->order_code);

        // Gửi email xác nhận cho khách (giống VNPay/MoMo)
        if ($booking->email) {
            try {
                $booking->load('room.hotel', 'payment');
                Mail::to($booking->email)->send(new PaymentConfirmed($booking));
                Mail::to(config('mail.from.address'))->send(new AdminNewBooking($booking));
            } catch (\Exception $e) {
                Log::error('Payment email failed (SePay): ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true]);
    }

    // =========================================================
    // ZaloPay
    // =========================================================

    /** IPN (callback) — ZaloPay gọi ngầm sau khi thanh toán */
    public function zalopayCallback(Request $request, ZalopayService $zalopay)
    {
        $data = $request->input('data', '');
        $mac  = $request->input('mac', '');

        if (!$zalopay->verifyCallback($data, $mac)) {
            Log::warning('ZaloPay callback: chữ ký không hợp lệ', ['mac' => $mac]);
            return response()->json(['return_code' => 0, 'return_message' => 'Invalid signature']);
        }

        $cbData    = $zalopay->parseCallbackData($data);
        $appTransId = $cbData['app_trans_id'] ?? '';
        $orderCode  = $zalopay->getOrderCode($appTransId);
        $status     = (int) ($cbData['status'] ?? 0);

        $booking = Booking::where('order_code', $orderCode)->first();

        if (!$booking) {
            Log::warning('ZaloPay callback: không tìm thấy booking', ['order_code' => $orderCode]);
            return response()->json(['return_code' => 0, 'return_message' => 'Order not found']);
        }

        if ($booking->payment && $booking->payment->payment_status === 'completed') {
            return response()->json(['return_code' => 1, 'return_message' => 'Already confirmed']);
        }

        DB::transaction(function () use ($booking, $status, $cbData, $appTransId) {
            $payStatus = $status === 1 ? 'completed' : 'failed';
            $paidAt    = $payStatus === 'completed' ? now() : null;

            if ($booking->payment) {
                $booking->payment->update([
                    'payment_status' => $payStatus,
                    'transaction_no' => $appTransId,
                    'paid_at'        => $paidAt,
                ]);
            } else {
                Payment::create([
                    'booking_id'     => $booking->id,
                    'hotel_id'       => $booking->room?->hotel_id,
                    'hotel_name'     => $booking->room?->hotel?->name,
                    'room_name'      => $booking->room?->room_name,
                    'method'         => 'zalopay',
                    'full_name'      => $booking->full_name,
                    'email'          => $booking->email,
                    'phone'          => $booking->phone,
                    'amount'         => $booking->total_price,
                    'payment_status' => $payStatus,
                    'transaction_no' => $appTransId,
                    'paid_at'        => $paidAt,
                ]);
            }

            if ($payStatus === 'completed') {
                $booking->update(['status' => 'confirmed']);
            }
        });

        if ($booking->email) {
            try {
                $booking->load('room.hotel', 'payment');
                if ($zalopay->isSuccess($status)) {
                    Mail::to($booking->email)->send(new PaymentConfirmed($booking));
                } else {
                    Mail::to($booking->email)->send(new PaymentFailed($booking));
                }
            } catch (\Exception $e) {
                Log::error('Payment email failed (ZaloPay): ' . $e->getMessage());
            }
        }

        return response()->json(['return_code' => 1, 'return_message' => 'success']);
    }

    /** Return URL — user được redirect về sau khi thanh toán ZaloPay */
    public function zalopayReturn(Request $request, ZalopayService $zalopay)
    {
        $params    = $request->all();
        $orderCode = $request->query('order_code');

        // Fallback: lấy từ apptransid nếu không có order_code param
        if (!$orderCode && !empty($params['apptransid'])) {
            $orderCode = $zalopay->getOrderCode($params['apptransid']);
        }

        $booking = $orderCode
            ? Booking::where('order_code', $orderCode)->with('room.hotel')->first()
            : null;

        if (!$booking) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn đặt phòng.');
        }

        $status = (int) ($params['status'] ?? 0);

        if ($zalopay->isSuccess($status)) {
            return redirect()->route('booking.my')
                ->with('success', "Thanh toán ZaloPay thành công! Đặt phòng #{$orderCode} đã được xác nhận.");
        }

        return redirect()->route('payment.show', $booking)
            ->with('error', 'Thanh toán ZaloPay không thành công. Vui lòng thử lại.');
    }

    /** Return URL — user được redirect về */
    public function momoReturn(Request $request, MoMoService $momo)
    {
        $data      = $request->all();
        $orderCode = $momo->getOrderCode($data);
        $booking   = Booking::where('order_code', $orderCode)->with('room.hotel')->first();

        if (!$booking) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn đặt phòng.');
        }

        if ($momo->isSuccess($data)) {
            return redirect()->route('booking.my')
                ->with('success', "Thanh toán MoMo thành công! Đặt phòng #{$orderCode} đã được xác nhận.");
        }

        return redirect()->route('payment.show', $booking)
            ->with('error', 'Thanh toán MoMo không thành công. Vui lòng thử lại.');
    }
}
