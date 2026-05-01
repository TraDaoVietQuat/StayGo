<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReplaceHotelsSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: skip nếu đã có dữ liệu (tránh re-seed khi container restart)
        if (DB::table('hotels')->count() > 0) {
            $this->command->info('Hotels already seeded, skipping.');
            return;
        }

        $now = Carbon::now();

        // ── 1. Xóa dữ liệu cũ (theo thứ tự FK) ──────────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('bookings')->truncate();
        DB::table('reviews')->truncate();
        DB::table('favorites')->truncate();
        DB::table('hotel_images')->truncate();
        DB::table('rooms')->truncate();
        DB::table('hotels')->truncate();
        DB::table('locations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Đã xóa dữ liệu cũ.');

        // ── 2. Tạo locations mới ─────────────────────────────────────────
        $locIds = [];
        $locations = [
            ['name' => 'Đà Lạt',   'description' => 'Thành phố ngàn hoa, khí hậu mát mẻ quanh năm, thiên đường du lịch Tây Nguyên.', 'image' => 'background_ks.jpg'],
            ['name' => 'Nha Trang', 'description' => 'Thành phố biển nổi tiếng với bãi cát trắng, làn nước trong xanh và ẩm thực hải sản phong phú.', 'image' => 'ks-bien1.jpg'],
            ['name' => 'Vũng Tàu', 'description' => 'Thành phố biển gần TP.HCM, điểm nghỉ dưỡng cuối tuần lý tưởng với bãi biển và hải sản tươi ngon.', 'image' => 'anhbien2.jpg'],
            ['name' => 'Đà Nẵng',  'description' => 'Thành phố đáng sống nhất Việt Nam, sở hữu bãi biển Mỹ Khê, cầu Rồng và gần Hội An – Huế.', 'image' => 'dn.jpg'],
        ];

        foreach ($locations as $loc) {
            $locIds[$loc['name']] = DB::table('locations')->insertGetId($loc);
        }

        $this->command->info('Đã tạo 4 locations.');

        // ── 3. Dữ liệu khách sạn từ Traveloka ───────────────────────────
        $hotels = [

            // ═══════════════ ĐÀ LẠT ═══════════════
            [
                'name'                => 'Ana Mandara Villas Dalat Resort & Spa',
                'type'                => 'resort',
                'address'             => 'Lê Lai, Phường 5, Đà Lạt, Lâm Đồng',
                'description'         => 'Resort boutique phong cách villa Pháp cổ điển giữa đồi thông Đà Lạt. Gần Thác Cam Ly và Làng hoa Vạn Thành, Ana Mandara mang lại trải nghiệm nghỉ dưỡng sang trọng, lãng mạn trong không gian thiên nhiên thơ mộng.',
                'location_id'         => $locIds['Đà Lạt'],
                'price'               => 2400000,
                'old_price'           => 2900000,
                'rating'              => 9.0,
                'review_text'         => 'Xuất sắc',
                'review_count'        => 412,
                'amenities'           => json_encode(['wifi','pool','spa','restaurant','breakfast','garden','parking','gym']),
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'checkin_time'        => '14:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => true,
                'image'               => 'background_ks.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Le Petit / Villa Room',  'bed_type' => '1 giường đôi',  'price' => 2400000, 'day_price' => 1080000, 'quantity' => 8,  'max_guests' => 3],
                    ['room_name' => 'Villa Studio / Suite',   'bed_type' => '1 giường King', 'price' => 3800000, 'day_price' => 1710000, 'quantity' => 5,  'max_guests' => 4],
                ],
            ],
            [
                'name'                => 'Colline Hotel',
                'type'                => 'hotel',
                'address'             => '10 Phan Bội Châu, Phường 1, Đà Lạt, Lâm Đồng',
                'description'         => 'Khách sạn boutique tọa lạc ngay trung tâm Đà Lạt, cách Chợ Đêm và Hồ Xuân Hương chỉ vài bước chân. Thiết kế hiện đại ấm cúng, phù hợp cho cặp đôi và gia đình nhỏ muốn khám phá thành phố ngàn hoa.',
                'location_id'         => $locIds['Đà Lạt'],
                'price'               => 1500000,
                'old_price'           => 1900000,
                'rating'              => 8.4,
                'review_text'         => 'Rất tốt',
                'review_count'        => 287,
                'amenities'           => json_encode(['wifi','restaurant','breakfast','parking','ac']),
                'cancellation_policy' => 'Hủy miễn phí trước 24 giờ nhận phòng.',
                'checkin_time'        => '14:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => false,
                'image'               => 'promo.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Superior / Deluxe', 'bed_type' => '1 đôi / 2 đơn',  'price' => 1500000, 'day_price' => 675000,  'quantity' => 12, 'max_guests' => 3],
                    ['room_name' => 'Studio',            'bed_type' => '1 đôi + Sofa',    'price' => 2800000, 'day_price' => 1260000, 'quantity' => 5,  'max_guests' => 4],
                ],
            ],
            [
                'name'                => 'New Life Hotel Đà Lạt',
                'type'                => 'hotel',
                'address'             => '6 Đống Đa, Phường 3, Đà Lạt, Lâm Đồng',
                'description'         => 'Khách sạn 3 sao gần Cáp treo Đà Lạt và Đồi Robin, lý tưởng cho nhóm bạn và gia đình muốn trải nghiệm hoạt động ngoài trời. Phòng Superior/Deluxe rộng rãi, view đồi thông thoáng mát.',
                'location_id'         => $locIds['Đà Lạt'],
                'price'               => 950000,
                'old_price'           => 1200000,
                'rating'              => 7.9,
                'review_text'         => 'Tốt',
                'review_count'        => 163,
                'amenities'           => json_encode(['wifi','parking','restaurant','ac']),
                'cancellation_policy' => 'Hủy miễn phí trước 24 giờ nhận phòng.',
                'checkin_time'        => '14:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => false,
                'image'               => 'ks-bien2.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Superior / Deluxe', 'bed_type' => '1 đôi / 2 đơn', 'price' => 950000, 'day_price' => 427500, 'quantity' => 15, 'max_guests' => 3],
                ],
            ],

            // ═══════════════ NHA TRANG ═══════════════
            [
                'name'                => 'Amiana Resort Nha Trang',
                'type'                => 'resort',
                'address'             => 'Phạm Văn Đồng, Vĩnh Hòa, Nha Trang, Khánh Hòa',
                'description'         => 'Resort 5 sao ẩn mình bên bến du thuyền Ana Marina, sở hữu bãi biển riêng và hồ bơi vô cực tuyệt đẹp. Amiana mang đến kỳ nghỉ dưỡng đẳng cấp quốc tế ngay trên bờ biển Nha Trang, với các villa riêng biệt và dịch vụ spa cao cấp.',
                'location_id'         => $locIds['Nha Trang'],
                'price'               => 4800000,
                'old_price'           => 5800000,
                'rating'              => 9.1,
                'review_text'         => 'Xuất sắc',
                'review_count'        => 538,
                'amenities'           => json_encode(['wifi','pool','spa','restaurant','bar','breakfast','beach','gym','ac','parking']),
                'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                'checkin_time'        => '14:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => true,
                'image'               => 'ks-bien1.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Ocean / Garden Villa',       'bed_type' => '1 giường King', 'price' => 4800000, 'day_price' => 2160000, 'quantity' => 10, 'max_guests' => 3],
                    ['room_name' => 'Pool Villa (Hồ bơi riêng)',  'bed_type' => '1 giường King', 'price' => 9500000, 'day_price' => 4275000, 'quantity' => 5,  'max_guests' => 2],
                ],
            ],
            [
                'name'                => 'Queen Ann Nha Trang',
                'type'                => 'hotel',
                'address'             => '100 Trần Phú, Lộc Thọ, Nha Trang, Khánh Hòa',
                'description'         => 'Khách sạn 4 sao tọa lạc trên đường Trần Phú sầm uất, gần Tháp Trầm Hương và bãi biển Nha Trang. 100% phòng có view biển, thích hợp cho cặp đôi và gia đình muốn tận hưởng khung cảnh biển mỗi sáng.',
                'location_id'         => $locIds['Nha Trang'],
                'price'               => 1400000,
                'old_price'           => 1700000,
                'rating'              => 8.3,
                'review_text'         => 'Rất tốt',
                'review_count'        => 321,
                'amenities'           => json_encode(['wifi','pool','restaurant','breakfast','ac','parking']),
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'checkin_time'        => '14:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => false,
                'image'               => 'anhbien.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Deluxe / Executive', 'bed_type' => '1 đôi / 2 đơn',  'price' => 1400000, 'day_price' => 630000,  'quantity' => 20, 'max_guests' => 4],
                    ['room_name' => 'Suite (VIP)',         'bed_type' => '1 giường King',  'price' => 3200000, 'day_price' => 1440000, 'quantity' => 6,  'max_guests' => 4],
                ],
            ],
            [
                'name'                => 'Citadines Bayfront Nha Trang',
                'type'                => 'hotel',
                'address'             => '62 Trần Phú, Lộc Thọ, Nha Trang, Khánh Hòa',
                'description'         => 'Căn hộ dịch vụ 4 sao theo phong cách serviced apartment, gần Vincom Nha Trang và Phố Tây. Các phòng Studio có bếp nhỏ và căn hộ 2 phòng ngủ phù hợp cho gia đình lưu trú dài ngày.',
                'location_id'         => $locIds['Nha Trang'],
                'price'               => 1550000,
                'old_price'           => 1900000,
                'rating'              => 8.5,
                'review_text'         => 'Rất tốt',
                'review_count'        => 274,
                'amenities'           => json_encode(['wifi','pool','restaurant','breakfast','ac','parking','gym']),
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'checkin_time'        => '15:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => false,
                'image'               => 'ks-bien2.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Studio Heritage', 'bed_type' => '1 đôi / 2 đơn',      'price' => 1550000, 'day_price' => 697500,  'quantity' => 15, 'max_guests' => 3],
                    ['room_name' => 'Căn hộ 2 PN',    'bed_type' => '1 King + 2 đơn',      'price' => 4100000, 'day_price' => 1845000, 'quantity' => 8,  'max_guests' => 6],
                ],
            ],

            // ═══════════════ VŨNG TÀU ═══════════════
            [
                'name'                => 'The Imperial Hotel Vũng Tàu',
                'type'                => 'hotel',
                'address'             => '159 Thùy Vân, Thắng Tam, Vũng Tàu, Bà Rịa - Vũng Tàu',
                'description'         => 'Khách sạn 5 sao phong cách Hoàng gia Anh tọa lạc trên bãi biển Bãi Sau, gần chợ đêm hải sản sầm uất. Kiến trúc cổ điển sang trọng với hồ bơi ngoài trời và nhà hàng đa ẩm thực.',
                'location_id'         => $locIds['Vũng Tàu'],
                'price'               => 2850000,
                'old_price'           => 3400000,
                'rating'              => 8.8,
                'review_text'         => 'Tuyệt vời',
                'review_count'        => 489,
                'amenities'           => json_encode(['wifi','pool','spa','restaurant','bar','breakfast','gym','parking','ac','beach']),
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'checkin_time'        => '14:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => true,
                'image'               => 'background_ks.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Deluxe',      'bed_type' => '1 đôi / 2 đơn',  'price' => 2850000, 'day_price' => 1282500, 'quantity' => 25, 'max_guests' => 3],
                    ['room_name' => 'Grand Suite',  'bed_type' => '1 King + Sofa',  'price' => 5500000, 'day_price' => 2475000, 'quantity' => 8,  'max_guests' => 4],
                ],
            ],
            [
                'name'                => 'Premier Pearl Hotel Vũng Tàu',
                'type'                => 'hotel',
                'address'             => '69-69A Thùy Vân, Phường 2, Vũng Tàu, Bà Rịa - Vũng Tàu',
                'description'         => 'Khách sạn 4 sao hướng biển, gần Tượng Chúa Kitô và Mũi Nghinh Phong. Phòng Deluxe Ocean với cửa kính sát đất cho tầm nhìn biển tuyệt đẹp, Triple Ocean phù hợp cho nhóm bạn.',
                'location_id'         => $locIds['Vũng Tàu'],
                'price'               => 1800000,
                'old_price'           => 2200000,
                'rating'              => 8.2,
                'review_text'         => 'Rất tốt',
                'review_count'        => 218,
                'amenities'           => json_encode(['wifi','pool','restaurant','breakfast','ac','parking']),
                'cancellation_policy' => 'Hủy miễn phí trước 24 giờ nhận phòng.',
                'checkin_time'        => '14:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => false,
                'image'               => 'anhbien2.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Deluxe Ocean',  'bed_type' => '1 giường đôi', 'price' => 1800000, 'day_price' => 810000,  'quantity' => 20, 'max_guests' => 3],
                    ['room_name' => 'Triple Ocean',  'bed_type' => '3 giường đơn', 'price' => 2600000, 'day_price' => 1170000, 'quantity' => 8,  'max_guests' => 3],
                ],
            ],
            [
                'name'                => 'Marina Bay Vung Tau',
                'type'                => 'hotel',
                'address'             => '115 Trần Phú, Phường 5, Vũng Tàu, Bà Rịa - Vũng Tàu',
                'description'         => 'Khách sạn 4 sao ven biển Bãi Trước, gần Gành Hào và Bãi Dầu. Tầm view hoàng hôn cực đẹp từ phòng Deluxe/Premier, không gian hiện đại thoáng mát, lý tưởng cho chuyến cuối tuần từ TP.HCM.',
                'location_id'         => $locIds['Vũng Tàu'],
                'price'               => 2700000,
                'old_price'           => 3200000,
                'rating'              => 8.1,
                'review_text'         => 'Rất tốt',
                'review_count'        => 196,
                'amenities'           => json_encode(['wifi','pool','restaurant','breakfast','ac','parking','bar']),
                'cancellation_policy' => 'Hủy miễn phí trước 24 giờ nhận phòng.',
                'checkin_time'        => '14:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => false,
                'image'               => 'promo.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Deluxe / Premier', 'bed_type' => '1 đôi / 2 đơn', 'price' => 2700000, 'day_price' => 1215000, 'quantity' => 18, 'max_guests' => 3],
                ],
            ],

            // ═══════════════ ĐÀ NẴNG ═══════════════
            [
                'name'                => 'InterContinental Danang Sun Peninsula Resort',
                'type'                => 'resort',
                'address'             => 'Bán đảo Sơn Trà, Đà Nẵng',
                'description'         => 'Resort 5 sao đẳng cấp thế giới nằm ẩn mình trên bán đảo Sơn Trà xanh tươi, gần Chùa Linh Ứng linh thiêng. Kiến trúc bậc thang hòa cùng thiên nhiên, hồ bơi vô cực, nhà hàng trên vách núi và bãi biển riêng hoàn toàn riêng tư.',
                'location_id'         => $locIds['Đà Nẵng'],
                'price'               => 14000000,
                'old_price'           => 17000000,
                'rating'              => 9.4,
                'review_text'         => 'Xuất sắc',
                'review_count'        => 862,
                'amenities'           => json_encode(['wifi','pool','spa','restaurant','bar','breakfast','beach','gym','ac','parking']),
                'cancellation_policy' => 'Hủy miễn phí trước 7 ngày nhận phòng.',
                'checkin_time'        => '15:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => true,
                'image'               => 'tour-du-lich-da-nang-1.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Classic / Terrace Suite', 'bed_type' => '1 giường King lớn', 'price' => 14000000, 'day_price' => 6300000, 'quantity' => 12, 'max_guests' => 3],
                ],
            ],
            [
                'name'                => 'Novotel Danang Premier Han River',
                'type'                => 'hotel',
                'address'             => '36 Bạch Đằng, Hải Châu, Đà Nẵng',
                'description'         => 'Khách sạn 5 sao chuẩn quốc tế của AccorHotels, tọa lạc ngay bên sông Hàn với tầm nhìn đẹp ra Cầu Rồng và Cầu Sông Hàn. Căn hộ 2 phòng ngủ rộng rãi phù hợp gia đình; phòng Superior/Executive view sông thơ mộng.',
                'location_id'         => $locIds['Đà Nẵng'],
                'price'               => 2800000,
                'old_price'           => 3400000,
                'rating'              => 8.6,
                'review_text'         => 'Tuyệt vời',
                'review_count'        => 614,
                'amenities'           => json_encode(['wifi','pool','spa','restaurant','bar','breakfast','gym','ac','parking']),
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'checkin_time'        => '14:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => false,
                'image'               => 'dn.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Superior / Executive', 'bed_type' => '1 đôi / 2 đơn', 'price' => 2800000, 'day_price' => 1260000, 'quantity' => 30, 'max_guests' => 3],
                    ['room_name' => 'Căn hộ 2 PN',         'bed_type' => '1 King + 2 đơn', 'price' => 6500000, 'day_price' => 2925000, 'quantity' => 10, 'max_guests' => 6],
                ],
            ],
            [
                'name'                => 'Naman Retreat Đà Nẵng',
                'type'                => 'resort',
                'address'             => 'Đường Trường Sa, Ngũ Hành Sơn, Đà Nẵng',
                'description'         => 'Resort kiến trúc tre độc đáo nằm trên Đường Trường Sa, gần Ngũ Hành Sơn và chỉ 30 phút từ phố cổ Hội An. Naman nổi tiếng với hồ bơi dài 250m, spa tự nhiên và Pool Villa với hồ bơi riêng từng căn — trải nghiệm chữa lành giữa thiên nhiên.',
                'location_id'         => $locIds['Đà Nẵng'],
                'price'               => 4800000,
                'old_price'           => 5800000,
                'rating'              => 9.2,
                'review_text'         => 'Xuất sắc',
                'review_count'        => 723,
                'amenities'           => json_encode(['wifi','pool','spa','restaurant','breakfast','beach','gym','ac','parking','bar']),
                'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                'checkin_time'        => '14:00',
                'checkout_time'       => '12:00',
                'is_active'           => true,
                'is_weekend_deal'     => true,
                'image'               => 'hero-bg.jpg',
                'created_at'          => $now,
                'rooms' => [
                    ['room_name' => 'Babylon',    'bed_type' => '1 đôi / 2 đơn',  'price' => 4800000, 'day_price' => 2160000, 'quantity' => 15, 'max_guests' => 3],
                    ['room_name' => 'Pool Villa', 'bed_type' => '1 giường King',   'price' => 7000000, 'day_price' => 3150000, 'quantity' => 8,  'max_guests' => 3],
                ],
            ],
        ];

        // ── 4. Insert hotels + rooms ─────────────────────────────────────
        $hotelCount = 0;
        $roomCount  = 0;

        foreach ($hotels as $data) {
            $rooms = $data['rooms'];
            unset($data['rooms']);

            $hotelId = DB::table('hotels')->insertGetId($data);
            $hotelCount++;

            foreach ($rooms as $room) {
                $room['hotel_id'] = $hotelId;
                $room['image']    = $data['image'];
                DB::table('rooms')->insert($room);
                $roomCount++;
            }
        }

        // Xóa cache cũ
        DB::table('cache')->where('key', 'like', 'home.%')
                          ->orWhere('key', 'like', 'all.locations%')
                          ->delete();

        $this->command->info("✅ Hoàn tất: {$hotelCount} khách sạn, {$roomCount} loại phòng, 4 locations.");
    }
}
