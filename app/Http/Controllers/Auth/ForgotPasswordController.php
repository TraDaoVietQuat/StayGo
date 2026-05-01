<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'exists:users,email']], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không hợp lệ.',
            'email.exists'   => 'Email chưa được đăng ký.',
        ]);

        $key = 'forgot_otp_send:' . $request->ip() . ':' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['email' => "Bạn đã gửi quá nhiều yêu cầu. Thử lại sau {$seconds} giây."]);
        }
        RateLimiter::hit($key, 600);

        $user = User::where('email', $request->email)->first();
        $otp  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'   => $otp,
            'otp_expire' => now()->addMinutes(15),
        ]);

        Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($m) use ($user) {
            $m->to($user->email)->subject('Mã OTP đặt lại mật khẩu - StayGo');
        });

        session(['reset_email' => $request->email]);
        return redirect()->route('password.verify-otp')->with('success', 'Mã OTP đã được gửi đến email của bạn.');
    }

    public function showVerifyOtp()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.forgot');
        }
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => ['required', 'digits:6']], [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.digits'   => 'Mã OTP gồm 6 chữ số.',
        ]);

        $email = session('reset_email');

        $key = 'forgot_otp_verify:' . $email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['otp' => "Nhập sai quá nhiều lần. Thử lại sau {$seconds} giây."]);
        }

        $user  = User::where('email', $email)
            ->where('otp_code', $request->otp)
            ->where('otp_expire', '>', now())
            ->first();

        if (!$user) {
            RateLimiter::hit($key, 900);
            return back()->withErrors(['otp' => 'Mã OTP không đúng hoặc đã hết hạn.']);
        }

        RateLimiter::clear($key);

        session(['reset_verified' => true]);
        return redirect()->route('password.reset-form');
    }

    public function showResetForm()
    {
        if (!session('reset_email') || !session('reset_verified')) {
            return redirect()->route('password.forgot');
        }
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'min:6', 'confirmed'],
        ], [
            'password.required'  => 'Vui lòng nhập mật khẩu mới.',
            'password.min'       => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $user = User::where('email', session('reset_email'))->first();
        $user->update([
            'password'   => $request->password,
            'otp_code'   => null,
            'otp_expire' => null,
        ]);

        session()->forget(['reset_email', 'reset_verified']);
        return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công!');
    }
}
