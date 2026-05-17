<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HotelPartnerSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Partner@2026');
        $now      = now();

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

        foreach ($partners as $hotelId => [$bizName, $email, $contactName]) {
            // Tạo user nếu chưa tồn tại
            $existing = DB::table('users')->where('email', $email)->first();
            if (!$existing) {
                $userId = DB::table('users')->insertGetId([
                    'full_name'  => $contactName,
                    'email'      => $email,
                    'password'   => $password,
                    'role'       => 'hotel_partner',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $userId = $existing->id;
            }

            // Tạo profile nếu chưa tồn tại
            $profileExists = DB::table('hotel_partner_profiles')->where('user_id', $userId)->exists();
            if (!$profileExists) {
                DB::table('hotel_partner_profiles')->insert([
                    'user_id'         => $userId,
                    'status'          => 'active',
                    'business_name'   => $bizName,
                    'contact_name'    => $contactName,
                    'commission_rate' => 15.00,
                    'approved_at'     => $now,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
            }

            // Link hotel → partner_user_id nếu chưa link
            DB::table('hotels')
                ->where('id', $hotelId)
                ->whereNull('partner_user_id')
                ->update(['partner_user_id' => $userId]);
        }
    }
}
