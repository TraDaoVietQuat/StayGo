<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $inserts = [
            // hotel_id=1 — Ana Mandara Villas Dalat Resort & Spa
            1 => [
                ['caption' => 'Biệt thự Pháp cổ điển',      'file' => '10020212-055a64b0d1f0d5e2d0d82c68ab1e346d.webp'],
                ['caption' => 'Phòng nghỉ cao cấp',          'file' => '10020212-09ae1a0fcca2a4c8089d9ee517f580b8.webp'],
                ['caption' => 'Khu vườn xanh mát',           'file' => '10020212-5e3debf1a8894fcc6d673c4aedc62653.webp'],
                ['caption' => 'Bể bơi ngoài trời',           'file' => '10020212-6d32a8a1bf7893ce079a3c477eec9dee.webp'],
                ['caption' => 'Sảnh đón tiếp',               'file' => '10020212-718e70fffa68de9ce906c4e02c1fb399.webp'],
                ['caption' => 'View đồi thông Đà Lạt',       'file' => '10020212-7a8da2e98cbb3029c696c05155c2429a.webp'],
                ['caption' => 'Nhà hàng ẩm thực',            'file' => '10020212-8e84dcf0a37519a1537c98858798cd4c.webp'],
                ['caption' => 'Spa & thư giãn',               'file' => '10020212-91f38014c94b95bfa8f35366f3c1d950.webp'],
                ['caption' => 'Phòng Deluxe',                 'file' => '10020212-9823f05d3f7ac2ae961c2340698accd8.webp'],
                ['caption' => 'Khu nghỉ dưỡng',              'file' => '10020212-98dcb15baf69d8f48ef046da45b73c63.webp'],
            ],
            // hotel_id=2 — Colline Hotel
            2 => [
                ['caption' => 'Toàn cảnh Colline Đà Lạt',   'file' => '20014998-0ab6c06addeaa5d5821989db5ba95440.webp'],
                ['caption' => 'Phòng nghỉ view đồi',         'file' => '20014998-14fefdc84a98587a8cc660d02a39c3f1.webp'],
                ['caption' => 'Nhà hàng tầng cao',           'file' => '20014998-27737d12bf573e1e0089c4eb64e39519.webp'],
                ['caption' => 'Hồ bơi ngoài trời',           'file' => '20014998-2842fae77cf6fec82f8f7e4aee9c471e.webp'],
                ['caption' => 'Sảnh khách sạn',              'file' => '20014998-3be2fa327c131831695f9066a641ed84.webp'],
                ['caption' => 'Phòng Deluxe Garden',         'file' => '20014998-4d189fc291cef4c54bdd5340341c1f3b.webp'],
                ['caption' => 'View thung lũng Đà Lạt',     'file' => '20014998-607d6120b26d18dbe447a497856c18a9.webp'],
                ['caption' => 'Bar & Lounge',                'file' => '20014998-673985c33747a7a70da7ec41d695a4b9.webp'],
                ['caption' => 'Khu nghỉ dưỡng xanh',        'file' => '20014998-69a27cde5d35642cff5b910cd5a00877.webp'],
                ['caption' => 'Spa & Wellness',              'file' => '20014998-6fc277f83f9296eb2a6175075b535036.webp'],
            ],
            // hotel_id=3 — New Life Hotel Đà Lạt
            3 => [
                ['caption' => 'Khách sạn New Life Đà Lạt',  'file' => '20014998-13eddb7b8cc18e3f80a522e219354b20.webp'],
                ['caption' => 'Phòng nghỉ tiện nghi',        'file' => '20014998-2275fa263cdf7fdaa3a1bca85da7976e.webp'],
                ['caption' => 'View thành phố Đà Lạt',      'file' => '20014998-5106bc1b72e25d697121c207be512109.webp'],
                ['caption' => 'Nhà hàng phục vụ',            'file' => '20014998-6c699a617db78c5e22e05ba5510f2df2.webp'],
                ['caption' => 'Phòng Superior',              'file' => '20014998-7798bb59b73b170e4e44192d95ebb6da.webp'],
                ['caption' => 'Phòng Deluxe',                'file' => '20014998-bb262d1261e36d1cb1cd9afa76624dbc.webp'],
                ['caption' => 'Bể bơi ngoài trời',           'file' => '20014998-ead6b44c751c91aa33821ede1b390b69.webp'],
                ['caption' => 'Khu vực chung',               'file' => '20014998-ee0d8bc72325ba39614758ca34497323.webp'],
                ['caption' => 'Không gian nghỉ dưỡng',      'file' => '20020070-25b3255a07b1307c3d6d1a5b4a5db2a6.webp'],
                ['caption' => 'Phòng khách sạn hiện đại',   'file' => '20020070-2d5b21b85f04b1f012ffaa84bb9ba75b.webp'],
            ],
            // hotel_id=6 — Citadines Bayfront Nha Trang
            6 => [
                ['caption' => 'Citadines Nha Trang',         'file' => '10042791-2073x1380-FIT_AND_TRIM-4deae5db285ef808b747e81b00a3a997.webp'],
                ['caption' => 'Phòng Studio hiện đại',       'file' => '10042791-21c8463de5ec739dae4f4722c4437e7c.webp'],
                ['caption' => 'Hồ bơi tầng cao',             'file' => '10042791-8803404f981d917cfecb74fc11b767c7.webp'],
                ['caption' => 'View vịnh Nha Trang',         'file' => '10042791-914de367348d5c5680356a82e4ebc24a.webp'],
                ['caption' => 'Phòng 2 phòng ngủ',          'file' => '10042791-a35cb6663459c9e0ad118077b344e98a.webp'],
                ['caption' => 'Bếp tiện nghi',               'file' => '10042791-d28e6bc5af4b7759ffd5f6d8ede33d22.webp'],
                ['caption' => 'Sảnh đón tiếp',               'file' => '10042791-ebdbecc4e1cdf8b3e7754dd24d49bbc5.webp'],
                ['caption' => 'Khu vực sinh hoạt chung',    'file' => '67721092-4000x2680-FIT_AND_TRIM-78640cb664d51cb8a7e45b2edf520b2e.webp'],
            ],
            // hotel_id=7 — The Imperial Vung Tau Hotel & Resort
            7 => [
                ['caption' => 'Mặt tiền khách sạn Imperial', 'file' => '1.webp'],
                ['caption' => 'Hồ bơi tầng thượng',          'file' => '10019885-03134f2a349a3e2a3571223baef2188a.webp'],
                ['caption' => 'View biển Vũng Tàu',          'file' => '10019885-03f4863773dfdc3f66e21e0298d48a30.webp'],
                ['caption' => 'Phòng Deluxe Ocean View',     'file' => '10019885-0c97b23368bdd267630f90d79fdb22c7.webp'],
                ['caption' => 'Nhà hàng hải sản',            'file' => '10019885-1444c76b21c68142599c1774e34f52e6.webp'],
                ['caption' => 'Sảnh đón tiếp sang trọng',   'file' => '10019885-2d841912945da20fec7059295e7730b7.webp'],
                ['caption' => 'Gym & Fitness',               'file' => '10019885-3bdac553999c894e9ffa059583968376.webp'],
                ['caption' => 'Spa cao cấp',                 'file' => '10019885-4105031d797bf4dd6ffc7e9db4e99cb9.webp'],
                ['caption' => 'Bar & Lounge',                'file' => '10019885-5a88b915961acc6b4549ece0a4de6b85.webp'],
                ['caption' => 'Phòng Suite',                 'file' => '10019885-62b98152a57973ce0f34625e6d6bbc73.webp'],
            ],
            // hotel_id=8 — Premier Pearl Hotel Vũng Tàu
            8 => [
                ['caption' => 'Premier Pearl Vũng Tàu',      'file' => '20048096-0368d53d73dc502039060197fd00cc0d.webp'],
                ['caption' => 'Hồ bơi ngoài trời',           'file' => '20048096-043510db4a5c0cbcb30a6d467a7dc6b7.webp'],
                ['caption' => 'Nhà hàng Premier',            'file' => '20048096-062234a60974803cddcaf3b8160c3c53.webp'],
                ['caption' => 'View biển từ phòng',          'file' => '20048096-0a9b35b9a8454cc45128184df69d2df4.webp'],
                ['caption' => 'Phòng Classic Twin',          'file' => '20048096-0aca4af013b6f1f6633a9d2c8ed24a9f.webp'],
                ['caption' => 'Gym & Wellness',              'file' => '20048096-0ccbae7f8c2d45c8c02336d69d1ef8e9.webp'],
                ['caption' => 'Phòng Signature Suite',       'file' => '20048096-10d6bcb6c4f7100541ad460b16fdc12b.webp'],
                ['caption' => 'Khu vực ăn sáng',             'file' => '20048096-183232543746d9fbf3cd5d51ab6a51e9.webp'],
                ['caption' => 'Sảnh đón khách',              'file' => '20048096-1bf5db1c8552c65225820fa20f635e98.webp'],
                ['caption' => 'Bar & Lounge tầng cao',       'file' => '20048096-1c3a6616fd07eb83a0c1f60ad86bd4d7.webp'],
            ],
            // hotel_id=9 — Marina Bay Vung Tau Resort
            9 => [
                ['caption' => 'Marina Bay Resort Vũng Tàu', 'file' => '20021765-0a474aa3d594cfddbd91af9562b59f63.webp'],
                ['caption' => 'Hồ bơi vô cực hướng biển',  'file' => '20021765-212f5c0649396852bb3970723ce40932.webp'],
                ['caption' => 'View vịnh biển',              'file' => '20021765-2b6987e0940b16f314317dc1248b4710.webp'],
                ['caption' => 'Phòng Premier Ocean View',   'file' => '20021765-4042d52d339541f9b75bd64646c4006a.webp'],
                ['caption' => 'Khu vực nghỉ dưỡng',        'file' => '20021765-487734b590414fd7b161018747dda980.webp'],
                ['caption' => 'Bể bơi ngoài trời',          'file' => '20021765-48bb51908de4501704962648026e1312.webp'],
                ['caption' => 'Nhà hàng ven biển',          'file' => '20021765-53a791e8290803fb86ea6ab321eb8879.webp'],
                ['caption' => 'Sảnh đón tiếp',              'file' => '20021765-a012996122887667f0c7c6790be8e2bc.webp'],
                ['caption' => 'Phòng nghỉ cao cấp',         'file' => '20021765-a3ac108009d7d73fb01bd6e4dc3ee9cc.webp'],
                ['caption' => 'Khu vực thư giãn',           'file' => '20021765-a3d5291dab5767582f2b0cf371edfde8.webp'],
            ],
            // hotel_id=10 — InterContinental Danang Sun Peninsula Resort
            10 => [
                ['caption' => 'Toàn cảnh khu nghỉ dưỡng',  'file' => '87906532_XL.webp'],
                ['caption' => 'Bãi biển riêng',             'file' => '88954761_XL.webp'],
                ['caption' => 'Hồ bơi vô cực',              'file' => '91064536_XL.webp'],
                ['caption' => 'Phòng King Classic',         'file' => 'DADHA_2154998988_3957752878_R.webp'],
                ['caption' => 'Nhà hàng CITRON',            'file' => 'DADHA_2803299587_1414967158_T.webp'],
                ['caption' => 'Sảnh trung tâm',             'file' => 'DADHA_5377829859_O.webp'],
                ['caption' => 'Khu nghỉ dưỡng trên đồi',  'file' => 'DADHA_5377833395_O.webp'],
                ['caption' => 'View vịnh Đà Nẵng',         'file' => 'DADHA_6869187100_O.webp'],
                ['caption' => 'Spa đẳng cấp 5 sao',        'file' => 'DADHA_8126787839_O.webp'],
                ['caption' => 'Sunset Bar',                 'file' => 'DADHA_8145999471_O.webp'],
            ],
            // hotel_id=11 — Novotel Danang Premier Han River
            11 => [
                ['caption' => 'Novotel Đà Nẵng',            'file' => '1de43eb3ed1c35b8f441188a15d23a0a.webp'],
                ['caption' => 'View sông Hàn',              'file' => '45c148a7993fce52b682001b00c97ec0.webp'],
                ['caption' => 'Phòng nghỉ Superior',        'file' => '49ee044a7e531ff4d24bd5bf5430373d.webp'],
                ['caption' => 'Hồ bơi tầng thượng',        'file' => '6e0b5c9b204a16a84c2544a7857e21b2.jpg'],
                ['caption' => 'Nhà hàng tầng cao',          'file' => '7577822f07de6940b12f4214fd1cf702.webp'],
                ['caption' => 'Sảnh đón tiếp',              'file' => '763954495.jpg'],
                ['caption' => 'Bar & Lounge',               'file' => 'bf5e6378cc7f7867fdf4126adc18012e.webp'],
            ],
            // hotel_id=12 — Naman Retreat Đà Nẵng
            12 => [
                ['caption' => 'Naman Retreat Đà Nẵng',      'file' => '10021706-21401db45415a46cea56c5cfd3983d9d.webp'],
                ['caption' => 'Bể bơi dài 100m',            'file' => '10021706-26e395b9088abe8eb10ab2ae4806c485.webp'],
                ['caption' => 'Villa bãi biển riêng',       'file' => '10021706-2b2e4dff208711ff8466db3ee30c1a75.webp'],
                ['caption' => 'Không gian thiền định',      'file' => '10021706-424499f7f7104d36a661fc2192889ad0.webp'],
                ['caption' => 'Nhà hàng Hay Hay',           'file' => '10021706-5949575f05cf19979e45b7ad283aed09.webp'],
                ['caption' => 'Khu vực thư giãn',           'file' => '10021706-62c045e1eedc6a073724132cbe901dae.webp'],
                ['caption' => 'Phòng 1BR Apartment',        'file' => '10021706-68fa9b42f9dc994e121faa99d1536877.webp'],
                ['caption' => 'Pool Villa',                 'file' => '10021706-933961f2cb1c1954cc8da2465b9f66fc.webp'],
                ['caption' => 'Khu nghỉ dưỡng xanh',       'file' => '10021706-9d0e3a9333a7d63ab1d641f730c113e7.webp'],
                ['caption' => 'Bãi biển Non Nước',          'file' => '10021706-aaabb5a3cade0f1bb282333732e0bfd8.webp'],
                ['caption' => 'Spa & Wellness',             'file' => '10021706-f50f8be356958f5da580a05a7a6e5f39.webp'],
            ],
        ];

        $folderMap = [
            1  => 'Ana Mandara Villas Dalat Resort & Spa',
            2  => 'Colline Dalat',
            3  => 'New Life Hotel Đà Lạt',
            6  => 'Citadines Bayfront Nha Trang',
            7  => 'The Imperial Hotel Vũng Tàu',
            8  => 'Premier Pearl Hotel Vung Tau',
            9  => 'Marina Bay Vung Tau Resort',
            10 => 'InterContinental Danang Sun Peninsula Resort',
            11 => 'Novotel Danang Premier Han River',
            12 => 'Naman Retreat Resort',
        ];

        foreach ($inserts as $hotelId => $images) {
            if (DB::table('hotel_images')->where('hotel_id', $hotelId)->doesntExist()) {
                $folder = $folderMap[$hotelId];
                $rows = [];
                foreach ($images as $i => $img) {
                    $rows[] = [
                        'hotel_id'   => $hotelId,
                        'image'      => $folder . '/' . $img['file'],
                        'caption'    => $img['caption'],
                        'sort_order' => $i + 1,
                    ];
                }
                DB::table('hotel_images')->insert($rows);
            }
        }
    }

    public function down(): void
    {
        DB::table('hotel_images')->whereIn('hotel_id', [1, 2, 3, 6, 7, 8, 9, 10, 11, 12])->delete();
    }
};
