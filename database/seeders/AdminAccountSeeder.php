<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminAccountSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Xóa tài khoản admin cũ không dùng
        DB::table('users')->where('email', 'admin@gmail.com')->delete();

        // Tạo hoặc cập nhật tài khoản admin chính
        $existing = DB::table('users')->where('email', 'admin@staygo.vn')->first();
        if ($existing) {
            DB::table('users')->where('email', 'admin@staygo.vn')->update([
                'full_name'  => 'Admin',
                'password'   => Hash::make('StayGo@2026'),
                'role'       => 'admin',
                'updated_at' => $now,
            ]);
        } else {
            DB::table('users')->insert([
                'full_name'  => 'Admin',
                'email'      => 'admin@staygo.vn',
                'password'   => Hash::make('StayGo@2026'),
                'role'       => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
