<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromoController extends Controller
{
    /**
     * AJAX: kiểm tra mã giảm giá hợp lệ hay không
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code'   => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $promo = Promo::where('code', strtoupper($request->code))->first();

        if (!$promo) {
            return response()->json(['valid' => false, 'message' => 'Mã giảm giá không tồn tại.']);
        }

        $user = Auth::user();

        if (!$promo->isValid((float) $request->amount, $user)) {
            return response()->json(['valid' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.']);
        }

        $discountAmount = $promo->calculateDiscount((float) $request->amount);

        return response()->json([
            'valid'           => true,
            'code'            => $promo->code,
            'type'            => $promo->type,
            'value'           => $promo->value,
            'discount_amount' => $discountAmount,
            'message'         => 'Áp dụng thành công! Giảm ' . number_format($discountAmount, 0, ',', '.') . 'đ.',
        ]);
    }


    /**
     * Được gọi khi quét QR / nhấn link promo.
     * Lưu promo vào session rồi redirect về trang chủ (hoặc trang booking nếu có).
     */
    public function applyNewUser(Request $request)
    {
        // Chỉ áp dụng nếu đã đăng nhập
        if (! auth()->check()) {
            return redirect()->route('login')
                ->with('info', 'Vui lòng đăng nhập để nhận ưu đãi 10% cho đơn đặt phòng đầu tiên!');
        }

        $user = auth()->user();

        // Chỉ áp dụng nếu chưa từng đặt phòng thành công
        $hasBooking = \App\Models\Booking::where('user_id', $user->id)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($hasBooking) {
            return redirect()->route('home')
                ->with('info', 'Ưu đãi này chỉ áp dụng cho lần đặt phòng đầu tiên. Bạn đã sử dụng ưu đãi này rồi!');
        }

        session(['promo_new_user' => 'NEWUSER10', 'promo_new_user_at' => now()->toISOString()]);

        return redirect()->route('hotels.index')
            ->with('success', '🎉 Đã áp dụng ưu đãi 10% cho đơn đặt phòng đầu tiên của bạn!');
    }
}
