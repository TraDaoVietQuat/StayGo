<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    private static array $disposableDomains = [
        'mailinator.com','guerrillamail.com','guerrillamail.org','guerrillamail.net',
        'guerrillamail.de','guerrillamail.biz','guerrillamail.info','guerrillamailblock.com',
        'grr.la','spam4.me','yopmail.com','yopmail.fr','cool.fr.nf','jetable.fr.nf',
        'nospam.ze.tc','nomail.xl.cx','mega.zik.dj','speed.1s.fr','courriel.fr.nf',
        'moncourrier.fr.nf','monemail.fr.nf','monmail.fr.nf','tempmail.com',
        'temp-mail.org','temp-mail.io','throwam.com','trashmail.com','trashmail.at',
        'trashmail.io','trashmail.me','trashmail.net','trashmail.org','trash-mail.at',
        'fakeinbox.com','maildrop.cc','dispostable.com','10minutemail.com',
        'mailnull.com','spamgourmet.com','sharklasers.com','guerrillamail.com',
        'cuvox.de','dayrep.com','einrot.com','fleckens.hu','gustr.com','hashsend.com',
        'jourrapide.com','objectmail.com','obobbo.com','rhyta.com','superrito.com',
        'teleworm.us','throwam.com','armyspy.com','cuvox.de',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:100'],
            'email'     => [
                'required', 'email', 'unique:users,email',
                function ($attribute, $value, $fail) {
                    $domain = strtolower(substr(strrchr($value, '@'), 1));
                    if (in_array($domain, self::$disposableDomains, true)) {
                        $fail('Không được dùng email tạm thời hoặc email ảo. Vui lòng dùng Gmail, Outlook hoặc email thật.');
                    }
                },
            ],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Vui lòng nhập họ tên.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.email'        => 'Email không hợp lệ.',
            'email.unique'       => 'Email đã được đăng ký.',
            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.min'       => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ];
    }
}
