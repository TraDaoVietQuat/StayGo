<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetupDemoAccounts extends Command
{
    protected $signature   = 'staygo:setup-demo-accounts';
    protected $description = 'Tạo/reset tài khoản admin và hotel partner demo';

    public function handle(): void
    {
        $now = now();

        // ── 1. Admin ──────────────────────────────────────────────
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@staygo.vn'],
            [
                'full_name'  => 'Admin',
                'password'   => Hash::make('StayGo@2026'),
                'role'       => 'admin',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
        DB::table('users')->where('email', 'admin@gmail.com')->delete();
        $this->info('✓ Admin: admin@staygo.vn / StayGo@2026');

        // ── 2. Hotel Partners ─────────────────────────────────────
        $partners = [
            1  => ['Ana Mandara Villas Dalat Resort & Spa', 'anamandara@staygo.vn',      'Ana Mandara'],
            2  => ['Colline Hotel',                          'colline@staygo.vn',          'Colline Hotel'],
            3  => ['New Life Hotel Da Lat',                  'newlife@staygo.vn',          'New Life Hotel'],
            4  => ['Amiana Resort Nha Trang',                'amiana@staygo.vn',           'Amiana Resort'],
            5  => ['Queen Ann Nha Trang',                    'queenann@staygo.vn',         'Queen Ann'],
            6  => ['Citadines Bayfront Nha Trang',           'citadines@staygo.vn',        'Citadines Bayfront'],
            7  => ['The Imperial Vung Tau',                  'imperial@staygo.vn',         'The Imperial'],
            8  => ['Premier Pearl Hotel Vung Tau',           'premierpearl@staygo.vn',     'Premier Pearl'],
            9  => ['Marina Bay Vung Tau Resort',             'marinabay@staygo.vn',        'Marina Bay'],
            10 => ['InterContinental Danang',                'intercontinental@staygo.vn', 'InterContinental'],
            11 => ['Novotel Danang Premier',                 'novotel@staygo.vn',          'Novotel Danang'],
            12 => ['Naman Retreat Da Nang',                  'naman@staygo.vn',            'Naman Retreat'],
        ];

        $pass = Hash::make('Partner@2026');

        foreach ($partners as $hotelId => [$bizName, $email, $contactName]) {
            DB::table('users')->updateOrInsert(
                ['email' => $email],
                [
                    'full_name'  => $contactName,
                    'password'   => $pass,
                    'role'       => 'hotel_partner',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $userId = DB::table('users')->where('email', $email)->value('id');

            DB::table('hotel_partner_profiles')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'status'          => 'active',
                    'business_name'   => $bizName,
                    'contact_name'    => $contactName,
                    'commission_rate' => 15.00,
                    'approved_at'     => $now,
                    'updated_at'      => $now,
                    'created_at'      => $now,
                ]
            );

            DB::table('hotels')->where('id', $hotelId)->update(['partner_user_id' => $userId]);

            $this->info("✓ Partner #{$hotelId}: {$email}");
        }

        $this->info('');
        $this->info('Hoàn thành! Mật khẩu partner: Partner@2026');
    }
}
