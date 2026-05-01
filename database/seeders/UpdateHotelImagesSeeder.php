<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateHotelImagesSeeder extends Seeder
{
    public function run(): void
    {
        // Gán ảnh thực cho từng khách sạn theo id
        $images = [
            1  => 'hotel1.jpg',        // Ana Mandara Villas Dalat
            2  => 'hotel2.jpg',        // Colline Hotel Đà Lạt
            3  => 'hotel3.jpg',        // New Life Hotel Đà Lạt
            4  => 'ks-bien1.jpg',      // Amiana Resort Nha Trang
            5  => 'anhbien.jpg',       // Queen Ann Nha Trang
            6  => 'ks-bien2.jpg',      // Citadines Bayfront Nha Trang
            7  => 'background_ks.jpg', // The Imperial Hotel Vũng Tàu
            8  => 'anhbien2.jpg',      // Premier Pearl Hotel Vũng Tàu
            9  => 'promo.jpg',         // Marina Bay Vung Tau
            10 => 'mykhe_beach.jpg',   // InterContinental Danang Sun Peninsula
            11 => 'dn.jpg',            // Novotel Danang Premier Han River
            12 => 'hero-bg.jpg',       // Naman Retreat Đà Nẵng
        ];

        foreach ($images as $id => $img) {
            DB::table('hotels')->where('id', $id)->update(['image' => $img]);
            $this->command->info("Hotel #{$id} → {$img}");
        }

        // Cập nhật ảnh cho rooms (dùng cùng ảnh khách sạn)
        foreach ($images as $hotelId => $img) {
            DB::table('rooms')->where('hotel_id', $hotelId)->update(['image' => $img]);
        }

        // Xóa cache
        DB::table('cache')->where('key', 'like', 'home.%')
                          ->orWhere('key', 'like', 'all.locations%')
                          ->orWhere('key', 'like', 'hotel.%')
                          ->delete();

        $this->command->info('✅ Đã cập nhật ảnh cho 12 khách sạn và phòng tương ứng.');
    }
}
