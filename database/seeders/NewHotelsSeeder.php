<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewHotelsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $hotels = [
            // ===== MĂNG ĐEN (location_id: 1) =====
            [
                'name'        => 'EPIC Resort & Hotel Măng Đen',
                'address'     => 'Đường bauxite, TT. Măng Đen, Kon Plông, Kon Tum',
                'description' => 'Resort & Hotel hiện đại tọa lạc tại trung tâm thị trấn Măng Đen, cung cấp phòng Standard và Deluxe tiêu chuẩn 4 sao với view rừng thông.',
                'location_id' => 1,
                'price'       => 650000,
                'old_price'   => 800000,
                'rating'      => 8.4,
                'review_count'=> 85,
                'review_text' => 'Rất tốt',
                'amenities'   => json_encode(['wifi','parking','restaurant','pool','gym']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Standard','bed_type'=>'Giường đôi','price'=>650000,'day_price'=>300000,'quantity'=>6,'max_guests'=>2],
                    ['room_name'=>'Deluxe','bed_type'=>'Giường King','price'=>1200000,'day_price'=>550000,'quantity'=>4,'max_guests'=>2],
                ],
            ],
            [
                'name'        => 'Sum Villa Măng Đen',
                'address'     => 'Đường du lịch số 1, TT. Măng Đen, Kon Plông, Kon Tum',
                'description' => 'Homestay villa phong cách núi rừng với các phòng Dorm, Phòng đơn và Deluxe, không gian trong lành gần hồ Đăk Ke.',
                'location_id' => 1,
                'price'       => 170000,
                'old_price'   => 220000,
                'rating'      => 8.2,
                'review_count'=> 63,
                'review_text' => 'Rất tốt',
                'amenities'   => json_encode(['wifi','parking','breakfast']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Dorm (4 người)','bed_type'=>'Giường tầng','price'=>170000,'day_price'=>80000,'quantity'=>4,'max_guests'=>4],
                    ['room_name'=>'Phòng đơn','bed_type'=>'Giường đơn','price'=>350000,'day_price'=>160000,'quantity'=>5,'max_guests'=>1],
                    ['room_name'=>'Deluxe','bed_type'=>'Giường đôi','price'=>1080000,'day_price'=>480000,'quantity'=>3,'max_guests'=>2],
                ],
            ],
            [
                'name'        => 'Eva Măng Đen Homestay',
                'address'     => 'Hàm Nghi, TT. Măng Đen, Kon Plông, Kon Tum',
                'description' => 'Trải nghiệm độc đáo với nhà trên cây và bungalow giữa rừng thông Măng Đen — điểm lưu trú được yêu thích cho các cặp đôi.',
                'location_id' => 1,
                'price'       => 140000,
                'old_price'   => 180000,
                'rating'      => 8.6,
                'review_count'=> 112,
                'review_text' => 'Tuyệt vời',
                'amenities'   => json_encode(['wifi','breakfast','garden']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Nhà trên cây','bed_type'=>'Giường đôi','price'=>700000,'day_price'=>320000,'quantity'=>3,'max_guests'=>2],
                    ['room_name'=>'Bungalow rừng','bed_type'=>'Giường đôi','price'=>140000,'day_price'=>70000,'quantity'=>5,'max_guests'=>2],
                ],
            ],
            [
                'name'        => 'Sakura Homestay Măng Đen',
                'address'     => 'Đường Xuân Diệu, TT. Măng Đen, Kon Plông, Kon Tum',
                'description' => 'Homestay phong cách Nhật Bản với bungalow gỗ ấm cúng và phòng đôi view vườn, không khí yên tĩnh giữa núi rừng Tây Nguyên.',
                'location_id' => 1,
                'price'       => 450000,
                'old_price'   => 560000,
                'rating'      => 8.3,
                'review_count'=> 74,
                'review_text' => 'Rất tốt',
                'amenities'   => json_encode(['wifi','parking','breakfast','garden']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Bungalow gỗ','bed_type'=>'Giường đôi','price'=>450000,'day_price'=>210000,'quantity'=>6,'max_guests'=>2],
                    ['room_name'=>'Phòng đôi Deluxe','bed_type'=>'Giường King','price'=>1500000,'day_price'=>680000,'quantity'=>3,'max_guests'=>2],
                ],
            ],
            [
                'name'        => 'Toki Măng Đen',
                'address'     => 'Khu du lịch Hồ Đăk Ke, TT. Măng Đen, Kon Plông, Kon Tum',
                'description' => 'Lưu trú phong cách sáng tạo với phòng Attic gác mái độc đáo và phòng Deluxe nhìn ra hồ Đăk Ke thơ mộng.',
                'location_id' => 1,
                'price'       => 800000,
                'old_price'   => 980000,
                'rating'      => 8.7,
                'review_count'=> 98,
                'review_text' => 'Tuyệt vời',
                'amenities'   => json_encode(['wifi','parking','restaurant','lake_view']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Attic (Gác mái)','bed_type'=>'Giường đôi','price'=>800000,'day_price'=>360000,'quantity'=>4,'max_guests'=>2],
                    ['room_name'=>'Deluxe view hồ','bed_type'=>'Giường King','price'=>1200000,'day_price'=>540000,'quantity'=>4,'max_guests'=>2],
                ],
            ],

            // ===== KON TUM (location_id: 2) =====
            [
                'name'        => 'Window 2 Hotel Kon Tum',
                'address'     => '131 Thi Sách, P. Thắng Lợi, TP. Kon Tum',
                'description' => 'Khách sạn boutique nằm trên phố Thi Sách, tiện nghi hiện đại với phòng Standard và Deluxe, gần trung tâm thành phố Kon Tum.',
                'location_id' => 2,
                'price'       => 400000,
                'old_price'   => 500000,
                'rating'      => 8.0,
                'review_count'=> 57,
                'review_text' => 'Tốt',
                'amenities'   => json_encode(['wifi','parking','breakfast']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Standard','bed_type'=>'Giường đôi','price'=>400000,'day_price'=>180000,'quantity'=>8,'max_guests'=>2],
                    ['room_name'=>'Deluxe','bed_type'=>'Giường King','price'=>600000,'day_price'=>280000,'quantity'=>5,'max_guests'=>2],
                ],
            ],
            [
                'name'        => 'Xã Nu Kon Tum Hotel',
                'address'     => '211 Lê Hồng Phong, P. Quyết Thắng, TP. Kon Tum',
                'description' => 'Khách sạn lấy cảm hứng từ văn hóa Tây Nguyên, cung cấp phòng Deluxe và Suite với thiết kế độc đáo mang đậm bản sắc địa phương.',
                'location_id' => 2,
                'price'       => 797000,
                'old_price'   => 950000,
                'rating'      => 8.5,
                'review_count'=> 89,
                'review_text' => 'Rất tốt',
                'amenities'   => json_encode(['wifi','parking','restaurant','breakfast']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Deluxe','bed_type'=>'Giường King','price'=>797000,'day_price'=>360000,'quantity'=>6,'max_guests'=>2],
                    ['room_name'=>'Suite','bed_type'=>'Giường King','price'=>1200000,'day_price'=>550000,'quantity'=>3,'max_guests'=>3],
                ],
            ],
            [
                'name'        => 'Khách sạn Konklor',
                'address'     => '155 Bắc Kạn, P. Thắng Lợi, TP. Kon Tum',
                'description' => 'Khách sạn bình dân gần cầu treo Kon Klor nổi tiếng, phù hợp cho du khách muốn khám phá làng văn hóa Ba Na và rừng Kon Chư Răng.',
                'location_id' => 2,
                'price'       => 350000,
                'old_price'   => 420000,
                'rating'      => 7.8,
                'review_count'=> 45,
                'review_text' => 'Tốt',
                'amenities'   => json_encode(['wifi','parking']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Phòng tiêu chuẩn','bed_type'=>'Giường đôi','price'=>350000,'day_price'=>160000,'quantity'=>10,'max_guests'=>2],
                    ['room_name'=>'Phòng gia đình','bed_type'=>'Nhiều giường','price'=>550000,'day_price'=>250000,'quantity'=>4,'max_guests'=>4],
                ],
            ],
            [
                'name'        => 'D Banlieue Homestay Kon Tum',
                'address'     => '155 Ngô Quyền, P. Thắng Lợi, TP. Kon Tum',
                'description' => 'Homestay phong cách Pháp cổ điển với phòng đôi có ban công nhìn ra đường phố yên tĩnh, gần nhà thờ gỗ Kon Tum.',
                'location_id' => 2,
                'price'       => 450000,
                'old_price'   => 540000,
                'rating'      => 8.4,
                'review_count'=> 68,
                'review_text' => 'Rất tốt',
                'amenities'   => json_encode(['wifi','breakfast','garden']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Phòng đôi ban công','bed_type'=>'Giường đôi','price'=>450000,'day_price'=>210000,'quantity'=>6,'max_guests'=>2],
                ],
            ],
            [
                'name'        => 'Home Sweet Homestay Kon Tum',
                'address'     => '105/2 Nguyễn Huệ, P. Thắng Lợi, TP. Kon Tum',
                'description' => 'Homestay ấm cúng như ở nhà, phù hợp cho gia đình và nhóm bạn với phòng tiêu chuẩn và phòng gia đình giá hợp lý.',
                'location_id' => 2,
                'price'       => 160000,
                'old_price'   => 200000,
                'rating'      => 8.1,
                'review_count'=> 52,
                'review_text' => 'Rất tốt',
                'amenities'   => json_encode(['wifi','parking','kitchen']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Phòng tiêu chuẩn','bed_type'=>'Giường đôi','price'=>160000,'day_price'=>75000,'quantity'=>5,'max_guests'=>2],
                    ['room_name'=>'Phòng gia đình','bed_type'=>'Nhiều giường','price'=>250000,'day_price'=>115000,'quantity'=>3,'max_guests'=>5],
                ],
            ],

            // ===== QUẢNG NGÃI (location_id: 3) =====
            [
                'name'        => 'Khách sạn Cẩm Thành Quảng Ngãi',
                'address'     => '01 Phạm Văn Đồng, TP. Quảng Ngãi',
                'description' => 'Khách sạn 3 sao trung tâm thành phố Quảng Ngãi, cung cấp phòng Superior, Deluxe và Suite với đầy đủ tiện nghi hiện đại.',
                'location_id' => 3,
                'price'       => 800000,
                'old_price'   => 980000,
                'rating'      => 8.3,
                'review_count'=> 130,
                'review_text' => 'Rất tốt',
                'amenities'   => json_encode(['wifi','parking','restaurant','pool','breakfast']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Superior','bed_type'=>'Giường đôi','price'=>800000,'day_price'=>360000,'quantity'=>8,'max_guests'=>2],
                    ['room_name'=>'Deluxe','bed_type'=>'Giường King','price'=>1100000,'day_price'=>500000,'quantity'=>6,'max_guests'=>2],
                    ['room_name'=>'Suite','bed_type'=>'Giường King','price'=>1500000,'day_price'=>680000,'quantity'=>3,'max_guests'=>3],
                ],
            ],
            [
                'name'        => 'Khách sạn Hùng Vương Quảng Ngãi',
                'address'     => '45 Hùng Vương, TP. Quảng Ngãi',
                'description' => 'Khách sạn lâu đời trên đường Hùng Vương, thuận tiện di chuyển đến các điểm tham quan trong thành phố và cảng biển Sa Kỳ.',
                'location_id' => 3,
                'price'       => 750000,
                'old_price'   => 900000,
                'rating'      => 7.9,
                'review_count'=> 96,
                'review_text' => 'Tốt',
                'amenities'   => json_encode(['wifi','parking','restaurant']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Standard','bed_type'=>'Giường đôi','price'=>750000,'day_price'=>340000,'quantity'=>10,'max_guests'=>2],
                    ['room_name'=>'Superior','bed_type'=>'Giường King','price'=>950000,'day_price'=>430000,'quantity'=>6,'max_guests'=>2],
                ],
            ],
            [
                'name'        => 'Mường Thanh Luxury Quảng Ngãi',
                'address'     => '26 Lê Lợi, TP. Quảng Ngãi',
                'description' => 'Khách sạn 5 sao đẳng cấp quốc tế của tập đoàn Mường Thanh, tọa lạc trung tâm Quảng Ngãi với hồ bơi vô cực, spa và nhà hàng sang trọng.',
                'location_id' => 3,
                'price'       => 1200000,
                'old_price'   => 1500000,
                'rating'      => 9.1,
                'review_count'=> 245,
                'review_text' => 'Xuất sắc',
                'amenities'   => json_encode(['wifi','parking','restaurant','pool','gym','spa','breakfast']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Deluxe King','bed_type'=>'Giường King','price'=>1200000,'day_price'=>550000,'quantity'=>20,'max_guests'=>2],
                    ['room_name'=>'Twin','bed_type'=>'Hai giường đơn','price'=>1200000,'day_price'=>550000,'quantity'=>15,'max_guests'=>2],
                    ['room_name'=>'Suite','bed_type'=>'Giường King','price'=>2500000,'day_price'=>1100000,'quantity'=>5,'max_guests'=>3],
                ],
            ],
            [
                'name'        => 'Làng Biển Homestay Mỹ Khê',
                'address'     => 'Bãi biển Mỹ Khê, TP. Quảng Ngãi',
                'description' => 'Homestay ven biển Mỹ Khê với phòng đôi hướng biển thơ mộng, cách bờ biển xanh chỉ vài bước chân — lý tưởng cho kỳ nghỉ biển.',
                'location_id' => 3,
                'price'       => 700000,
                'old_price'   => 850000,
                'rating'      => 8.5,
                'review_count'=> 118,
                'review_text' => 'Rất tốt',
                'amenities'   => json_encode(['wifi','breakfast','beach_access','garden']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Phòng đôi hướng biển','bed_type'=>'Giường đôi','price'=>700000,'day_price'=>320000,'quantity'=>8,'max_guests'=>2],
                ],
            ],
            [
                'name'        => 'Châu Tân Homestay Bình Sơn',
                'address'     => 'Biển Châu Tân, Bình Châu, Bình Sơn, Quảng Ngãi',
                'description' => 'Homestay độc lập tại bãi biển Châu Tân hoang sơ, có thể thuê nguyên căn hoặc phòng lẻ, thích hợp cho nhóm gia đình muốn nghỉ dưỡng yên tĩnh.',
                'location_id' => 3,
                'price'       => 1500000,
                'old_price'   => 1800000,
                'rating'      => 8.8,
                'review_count'=> 76,
                'review_text' => 'Tuyệt vời',
                'amenities'   => json_encode(['wifi','parking','kitchen','beach_access','bbq']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Nguyên căn','bed_type'=>'Nhiều giường','price'=>2000000,'day_price'=>900000,'quantity'=>2,'max_guests'=>8],
                    ['room_name'=>'Phòng lẻ','bed_type'=>'Giường đôi','price'=>1500000,'day_price'=>680000,'quantity'=>4,'max_guests'=>2],
                ],
            ],
            [
                'name'=>"Min's House Quảng Ngãi",
                'address'     => '210 Phan Bội Châu, TP. Quảng Ngãi',
                'description' => 'Boutique homestay phong cách decor hiện đại tối giản tại trung tâm Quảng Ngãi, cung cấp phòng đơn và đôi tiện nghi cho khách công tác và du lịch.',
                'location_id' => 3,
                'price'       => 400000,
                'old_price'   => 500000,
                'rating'      => 8.6,
                'review_count'=> 93,
                'review_text' => 'Tuyệt vời',
                'amenities'   => json_encode(['wifi','breakfast']),
                'is_active'   => true,
                'rooms' => [
                    ['room_name'=>'Phòng đơn','bed_type'=>'Giường đơn','price'=>400000,'day_price'=>180000,'quantity'=>5,'max_guests'=>1],
                    ['room_name'=>'Phòng đôi','bed_type'=>'Giường đôi','price'=>1000000,'day_price'=>450000,'quantity'=>5,'max_guests'=>2],
                ],
            ],
        ];

        foreach ($hotels as $data) {
            $rooms = $data['rooms'];
            unset($data['rooms']);
            $data['created_at'] = $now;

            $hotelId = DB::table('hotels')->insertGetId($data);

            foreach ($rooms as $room) {
                $room['hotel_id'] = $hotelId;
                DB::table('rooms')->insert($room);
            }
        }

        $this->command->info('Inserted ' . count($hotels) . ' new hotels with rooms.');
    }
}
