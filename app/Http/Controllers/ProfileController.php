<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('pages.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'email'     => ['required', 'email', 'unique:users,email,' . $user->id],
        ]);

        $user->update($request->only('full_name', 'phone', 'email'));
        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.min'              => 'Mật khẩu mới tối thiểu 6 ký tự.',
            'password.confirmed'        => 'Mật khẩu xác nhận không khớp.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update(['password' => $request->password]);
        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh.',
            'avatar.image'    => 'File phải là hình ảnh.',
            'avatar.mimes'    => 'Chỉ chấp nhận định dạng jpeg, png, jpg, gif, webp.',
            'avatar.max'      => 'Ảnh không được vượt quá 2MB.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Xóa avatar cũ nếu không phải ảnh mặc định
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Cập nhật ảnh đại diện thành công!');
    }
}
