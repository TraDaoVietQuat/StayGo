<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class OtpLoginController extends Controller
{
    public function showForm()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.otp-login');
    }

    public function send(Request $request)
    {
        if (Auth::check()) return redirect()->route('home');

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không hợp lệ.',
            'email.exists'   => 'Email chưa được đăng ký trong hệ thống.',
        ]);

        $key = 'login_otp_send:' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['email' => "Bạn đã gửi OTP quá nhiều lần. Thử lại sau {$seconds} giây."]);
        }
        RateLimiter::hit($key, 300);

        $user = User::where('email', $request->email)->first();
        $otp  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put('login_otp:' . $request->email, $otp, now()->addMinutes(10));

        try {
            Mail::to($user->email)->send(new OtpLogin($user, $otp));
        } catch (\Exception) {
            // không block nếu mail chưa cấu hình
        }

        session(['login_otp_email' => $request->email]);

        return redirect()->route('login.otp.verify')
            ->with('success', 'Mã OTP đã được gửi đến email của bạn. Có hiệu lực trong 10 phút.');
    }

    public function showVerify()
    {
        if (Auth::check()) return redirect()->route('home');
        if (!session('login_otp_email')) return redirect()->route('login.otp');
        return view('auth.otp-login-verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => ['required', 'digits:6']], [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.digits'   => 'Mã OTP gồm 6 chữ số.',
        ]);

        $email = session('login_otp_email');
        if (!$email) return redirect()->route('login.otp');

        $key = 'login_otp_verify:' . $email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            Cache::forget('login_otp:' . $email);
            session()->forget('login_otp_email');
            return redirect()->route('login.otp')
                ->withErrors(['email' => "Nhập sai OTP quá nhiều lần. Vui lòng yêu cầu mã mới sau {$seconds} giây."]);
        }

        $storedOtp = Cache::get('login_otp:' . $email);

        if (!$storedOtp || $storedOtp !== $request->otp) {
            RateLimiter::hit($key, 300);
            return back()->withErrors(['otp' => 'Mã OTP không đúng hoặc đã hết hạn.']);
        }

        RateLimiter::clear($key);
        Cache::forget('login_otp:' . $email);
        session()->forget('login_otp_email');

        $user = User::where('email', $email)->first();
        if (!$user) return redirect()->route('login.otp');

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect('/StayGoLaravel/public/admin');
        }

        return redirect()->intended(route('home'));
    }
}
