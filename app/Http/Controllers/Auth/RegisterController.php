<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class RegisterController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.register');
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

    public function register(RegisterRequest $request)
    {
        if (!$this->verifyRecaptcha($request)) {
            return back()->withErrors(['g-recaptcha-response' => 'Xác minh reCAPTCHA thất bại.'])->withInput();
        }

        $user = User::create([
            'full_name'   => $request->input('full_name'),
            'email'       => $request->input('email'),
            'phone'       => $request->input('phone'),
            'password'    => $request->input('password'),
            'role'        => 'user',
            'is_new_user' => true,
        ]);

        // Gửi email xác minh
        $this->sendVerificationEmail($user);

        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác minh tài khoản.');
    }

    /** Gửi email xác minh có signed URL */
    public function sendVerificationEmail(User $user): void
    {
        $url = URL::temporarySignedRoute(
            'email.verify',
            now()->addHours(24),
            ['id' => $user->id]
        );

        try {
            Mail::to($user->email)->send(new EmailVerification($user, $url));
        } catch (\Exception) {
            // Không fail nếu mail chưa cấu hình
        }
    }

    /** Xác minh từ link email */
    public function verify(Request $request, int $id)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Link xác minh không hợp lệ hoặc đã hết hạn.');
        }

        $user = User::findOrFail($id);

        if (!$user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
        }

        if (!Auth::check()) {
            Auth::login($user);
        }

        return redirect()->route('home')->with('success', 'Email đã được xác minh thành công!');
    }

    /** Gửi lại email xác minh */
    public function resend(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->email_verified_at) {
            return back()->with('info', 'Email của bạn đã được xác minh rồi.');
        }

        $this->sendVerificationEmail($user);

        return back()->with('success', 'Email xác minh đã được gửi lại. Vui lòng kiểm tra hộp thư.');
    }
}
