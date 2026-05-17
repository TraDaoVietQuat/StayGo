<?php

namespace App\Http\Controllers;

use App\Models\HotelPartnerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PartnerRegistrationController extends Controller
{
    public function showForm()
    {
        return view('partner.register');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required|string|max:20',
            'password'      => ['required', 'confirmed', Password::min(8)],
            'business_name' => 'required|string|max:150',
            'tax_code'      => 'nullable|string|max:30',
            'hotel_name'    => 'required|string|max:200',
        ], [
            'email.unique'         => 'Email này đã được đăng ký.',
            'password.confirmed'   => 'Mật khẩu xác nhận không khớp.',
            'password.min'         => 'Mật khẩu tối thiểu 8 ký tự.',
        ]);

        $user = User::create([
            'full_name'         => $request->full_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'role'              => 'hotel_partner',
            'email_verified_at' => now(),
        ]);

        HotelPartnerProfile::create([
            'user_id'       => $user->id,
            'status'        => 'pending',
            'business_name' => $request->business_name,
            'contact_name'  => $request->full_name,
            'contact_phone' => $request->phone,
            'tax_code'      => $request->tax_code,
            'notes'         => 'Khách sạn đề xuất: ' . $request->hotel_name,
        ]);

        return redirect()->route('partner.register.success');
    }

    public function success()
    {
        return view('partner.register-success');
    }
}
