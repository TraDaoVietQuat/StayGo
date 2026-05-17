<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\NewLoginAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    private function verifyRecaptcha(Request $request): bool
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);
        return $response->json('success') === true;
    }

    public function login(LoginRequest $request)
    {
        if (!$this->verifyRecaptcha($request)) {
            return back()->withErrors(['g-recaptcha-response' => 'Xác minh reCAPTCHA thất bại.'])->withInput($request->only('email'));
        }

        $key = 'login:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Quá nhiều lần đăng nhập thất bại. Thử lại sau {$seconds} giây.",
            ]);
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Gửi cảnh báo đăng nhập mới (non-blocking)
            try {
                $agent   = $request->userAgent() ?? '';
                $device  = str_contains($agent, 'Mobile') ? 'Mobile' : 'Desktop';
                $browser = match(true) {
                    str_contains($agent, 'Chrome') && !str_contains($agent, 'Edg') => 'Chrome',
                    str_contains($agent, 'Firefox') => 'Firefox',
                    str_contains($agent, 'Safari') && !str_contains($agent, 'Chrome') => 'Safari',
                    str_contains($agent, 'Edg') => 'Edge',
                    default => 'Browser khác',
                };
                Mail::to($user->email)->send(new NewLoginAlert(
                    user:    $user,
                    ip:      $request->ip(),
                    device:  $device,
                    browser: $browser,
                ));
            } catch (\Exception) {}

            if ($user->isAdmin()) {
                return redirect('/StayGoLaravel/public/admin');
            }
            return redirect()->intended(route('home'));
        }

        RateLimiter::hit($key, 900); // 15 phút

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
