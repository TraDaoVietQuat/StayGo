<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelImagesSeeder extends Seeder
{
    public function run(): void
    {
        // hotel_id => [folder, [file1, file2, ...]]
        // image 1 = main (updates hotels.image), 2-N = gallery (hotel_images)
        $map = [
            36 => ['sum villa',                      ['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg']],
            37 => ['eva mang den',                   ['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg']],
            39 => ['toki mang den',                  ['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg']],
            40 => ['hotel window 2',                 ['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg']],
            41 => ['xanu kontum',                    ['1.jpg','2.jpg','3.jpg','4.jpg']],
            43 => ['d banlieune',                    ['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg']],
            45 => ['cam thanh quang ngai',           ['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg']],
            46 => ['khach san hung vuong quang ngai',['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg']],
            47 => ['MuongThanh LS',                  ['1.jpg','2.jpg','3.jpg','4.jpg']],
            48 => ['lang bien',                      ['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg']],
            50 => ["min's house quang ngai",         ['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg']],
        ];

        foreach ($map as $hotelId => [$folder, $files]) {
            // Update main image on hotels table
            DB::table('hotels')->where('id', $hotelId)->update([
                'image' => $folder . '/' . $files[0],
            ]);

            // Insert hotel_images for all files (including first as sort_order=1)
            $rows = [];
            foreach ($files as $i => $file) {
                $rows[] = [
                    'hotel_id'   => $hotelId,
                    'image'      => $folder . '/' . $file,
                    'caption'    => '',
                    'sort_order' => $i + 1,
                ];
            }
            DB::table('hotel_images')->insert($rows);
        }
    }
}
