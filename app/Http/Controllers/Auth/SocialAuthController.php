<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeGoogle;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Đăng nhập Google thất bại: ' . $e->getMessage()]);
        }

        // Nếu user đang đăng nhập → link Google vào tài khoản hiện tại
        if (Auth::check()) {
            /** @var User $current */
            $current = Auth::user();
            if (!$current->google_id) {
                // Đảm bảo Google ID chưa được dùng bởi tài khoản khác
                $existing = User::where('google_id', $socialUser->getId())
                    ->where('id', '!=', $current->id)->first();
                if ($existing) {
                    return redirect()->route('profile.show')
                        ->withErrors(['google' => 'Tài khoản Google này đã được liên kết với tài khoản khác.']);
                }
                $current->update([
                    'google_id' => $socialUser->getId(),
                    'avatar'    => $current->avatar ?: $socialUser->getAvatar(),
                    'email_verified_at' => $current->email_verified_at ?? now(),
                ]);
                return redirect()->route('profile.show')
                    ->with('success', 'Đã liên kết tài khoản Google thành công!');
            }
            return redirect()->route('profile.show')
                ->with('info', 'Tài khoản đã được liên kết Google rồi.');
        }

        // Chưa đăng nhập → login / tạo tài khoản mới
        $user = User::firstOrCreate(
            ['google_id' => $socialUser->getId()],
            [
                'full_name'         => $socialUser->getName(),
                'email'             => $socialUser->getEmail(),
                'password'          => bcrypt(str()->random(32)),
                'avatar'            => $socialUser->getAvatar(),
                'role'              => 'user',
                'is_new_user'       => true,
                'email_verified_at' => now(),
            ]
        );

        // User tồn tại bằng email nhưng chưa có google_id → gắn vào
        if (!$user->wasRecentlyCreated && !$user->google_id) {
            $user->update([
                'google_id'         => $socialUser->getId(),
                'avatar'            => $user->avatar ?: $socialUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        }

        // Gửi email chào mừng cho tài khoản mới
        if ($user->wasRecentlyCreated && $user->email) {
            try {
                Mail::to($user->email)->send(new WelcomeGoogle($user));
            } catch (\Exception $e) {
                Log::error('WelcomeGoogle email failed: ' . $e->getMessage());
            }
        }

        Auth::login($user, true);
        return redirect()->intended(route('home'));
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $socialUser = Socialite::driver('facebook')->user();
        } catch (\Exception) {
            return redirect()->route('login')->withErrors(['email' => 'Đăng nhập Facebook thất bại.']);
        }

        $user = User::firstOrCreate(
            ['facebook_id' => $socialUser->getId()],
            [
                'full_name'   => $socialUser->getName(),
                'email'       => $socialUser->getEmail() ?? $socialUser->getId() . '@facebook.com',
                'password'    => bcrypt(str()->random(32)),
                'avatar'      => $socialUser->getAvatar(),
                'role'        => 'user',
                'is_new_user' => true,
            ]
        );

        Auth::login($user, true);
        return redirect()->intended(route('home'));
    }
}
