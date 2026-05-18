<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ══════════════════════════════════════════════════════════════════
        // 1. CẬP NHẬT THÔNG TIN CHÍNH 3 KHÁCH SẠN NHA TRANG
        // ══════════════════════════════════════════════════════════════════

        // ── Amiana Resort Nha Trang (hotel_id = 4) ───────────────────────
        DB::table('hotels')->where('id', 4)->update([
            'stars'          => 5,
            'rating'         => 9.1,
            'review_count'   => '968',
            'review_text'    => 'Xuất sắc',
            'ranking_title'  => 'Luxury Coastal Resort 5 sao',
            'description'    => 'Amiana Resort Nha Trang là khu nghỉ dưỡng chuẩn 5 sao quốc tế tọa lạc tại Vịnh Nha Trang với bãi biển riêng cát trắng mịn. Resort sở hữu hồ bơi nước biển tự nhiên rộng 2.500m² và hồ bơi nước ngọt vô cực 700m². Nội thất sử dụng vật liệu địa phương như gốm Bát Tràng, tre, đá và vỏ dừa, mang đậm bản sắc văn hóa Việt Nam. Đạt giải Luxury Coastal Resort Global Winner 2022 và Tripadvisor Travelers Choice nhiều năm liên tiếp.',
            'address'        => 'Đường Phạm Văn Đồng, Vịnh Nha Trang, Khánh Hòa',
            'checkin_time'   => '14:00',
            'checkout_time'  => '12:00',
            'price'          => 4800000,
            'amenities'      => json_encode(['wifi','pool','spa','restaurant','breakfast','parking','bar']),
        ]);

        // ── Queen Ann Nha Trang Hotel (hotel_id = 5) ─────────────────────
        DB::table('hotels')->where('id', 5)->update([
            'stars'          => 5,
            'rating'         => 9.0,
            'review_count'   => '383',
            'review_text'    => 'Xuất sắc',
            'ranking_title'  => 'Khách sạn 5 sao trung tâm Nha Trang',
            'description'    => 'Queen Ann Nha Trang Hotel là khách sạn 5 sao tọa lạc ngay trung tâm đường vàng Trần Phú với kiến trúc Art Deco phong cách châu Âu sang trọng. 276 phòng nghỉ 100% hướng biển, hồ bơi vô cực tầng 28 với tầm nhìn 360° toàn cảnh Vịnh Nha Trang. Có bãi biển riêng, 3 nhà hàng, 2 quầy bar, Queen Beauty Spa và Kids Club đầy đủ tiện ích 5 sao.',
            'address'        => '100 Trần Phú, Phường Lộc Thọ, Nha Trang, Khánh Hòa',
            'checkin_time'   => '14:00',
            'checkout_time'  => '12:00',
            'price'          => 1242000,
            'amenities'      => json_encode(['wifi','pool','spa','gym','restaurant','breakfast','bar','parking']),
        ]);

        // ── Citadines Bayfront Nha Trang (hotel_id = 6) ──────────────────
        DB::table('hotels')->where('id', 6)->update([
            'stars'          => 5,
            'rating'         => 8.9,
            'review_count'   => '611',
            'review_text'    => 'Xuất sắc',
            'ranking_title'  => 'Căn hộ dịch vụ 5 sao trực diện biển',
            'description'    => 'Citadines Bayfront Nha Trang là tòa nhà 27 tầng kết hợp khách sạn và căn hộ dịch vụ 5 sao, tọa lạc trực diện biển trên đường Trần Phú. Mang đến trải nghiệm sống như ở nhà với các căn hộ được trang bị bếp đầy đủ, máy giặt và ban công hướng biển. Hệ thống hồ bơi ngoài trời tầng 3 với tầm nhìn toàn cảnh biển, hồ bơi trẻ em, phòng gym và spa.',
            'address'        => '62 Trần Phú, Nha Trang, Khánh Hòa',
            'checkin_time'   => '15:00',
            'checkout_time'  => '12:00',
            'price'          => 1550000,
            'amenities'      => json_encode(['wifi','pool','spa','gym','restaurant','parking']),
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 2. CẬP NHẬT PHÒNG HIỆN CÓ (rooms 6–11) THEO DỮ LIỆU TRAVELOKA
        // ══════════════════════════════════════════════════════════════════

        $amDeluxe = json_encode(['breakfast','wifi','ac','tv','minibar','safe','desk',
                                 'bathtub','shower_standing','private_bathroom',
                                 'hair_dryer','bathrobe','toiletries','hot_water','free_bottled_water']);

        $amVilla  = json_encode(['breakfast','wifi','ac','tv','minibar','fridge','safe','desk',
                                 'bathtub','shower_standing','private_bathroom',
                                 'hair_dryer','bathrobe','toiletries','hot_water','free_bottled_water',
                                 'waiting_area','non_smoking']);

        $citStudio = json_encode(['wifi','ac','tv','safe','shower_standing','private_bathroom',
                                  'hair_dryer','toiletries','hot_water','free_bottled_water',
                                  'blackout_curtains']);

        $citApt    = json_encode(['wifi','ac','tv','fridge','safe','desk','shower_standing',
                                  'private_bathroom','hair_dryer','toiletries','hot_water',
                                  'free_bottled_water','waiting_area']);

        $qaStd     = json_encode(['breakfast','wifi','ac','tv','minibar','safe','desk',
                                  'bathtub','shower_standing','private_bathroom',
                                  'hair_dryer','bathrobe','toiletries','hot_water','free_bottled_water']);

        $qaSuite   = json_encode(['breakfast','wifi','ac','tv','minibar','fridge','safe','desk',
                                  'bathtub','shower_standing','private_bathroom',
                                  'hair_dryer','bathrobe','toiletries','hot_water','free_bottled_water',
                                  'waiting_area','non_smoking']);

        // Room 6 — Amiana Deluxe (cập nhật từ Ocean/Garden Villa)
        DB::table('rooms')->where('id', 6)->update([
            'room_name'           => 'Amiana Deluxe',
            'package_name'        => 'Amiana Deluxe - Sân vườn / Một phần Vịnh Nha Trang',
            'bed_type'            => '1 giường đôi King',
            'area'                => 55,
            'max_guests'          => 2,
            'max_children'        => 1,
            'quantity'            => 80,
            'price'               => 4800000,
            'is_refundable'       => 1,
            'is_sale'             => 1,
            'room_badge'          => 'Phổ biến',
            'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
            'room_amenities'      => $amDeluxe,
            'benefits'            => json_encode(['Bãi biển riêng', 'Hồ bơi 2.500m²', 'Bữa sáng cho 2', 'Ban công riêng']),
            'room_notes'          => json_encode('Phòng 55m² với ban công, view sân vườn hoặc một phần Vịnh Nha Trang. Nội thất từ vật liệu địa phương.'),
        ]);

        // Room 7 — Pool Villa (cập nhật diện tích và giá theo Traveloka)
        DB::table('rooms')->where('id', 7)->update([
            'room_name'           => 'Pool Villa',
            'package_name'        => 'Pool Villa - Hồ bơi riêng + View Vịnh',
            'bed_type'            => '1 giường đôi King',
            'area'                => 180,
            'max_guests'          => 3,
            'max_children'        => 1,
            'quantity'            => 30,
            'price'               => 18000000,
            'is_refundable'       => 1,
            'is_sale'             => 0,
            'room_badge'          => 'Sang trọng',
            'cancellation_policy' => 'Hủy miễn phí trước 5 ngày nhận phòng.',
            'room_amenities'      => $amVilla,
            'benefits'            => json_encode(['Hồ bơi riêng tư', 'Phòng khách riêng biệt', 'Bếp nhỏ', 'Butler service', 'Bữa sáng cho 2']),
            'room_notes'          => json_encode('Villa 180m² với hồ bơi riêng, view trực diện Vịnh Nha Trang, phòng khách tách biệt và bếp nhỏ. Phù hợp trăng mật.'),
        ]);

        // Room 8 — Deluxe Ocean View (cập nhật từ Deluxe/Executive)
        DB::table('rooms')->where('id', 8)->update([
            'room_name'           => 'Deluxe Ocean View',
            'package_name'        => 'Deluxe Ocean View - View biển trực diện + Bữa sáng',
            'bed_type'            => '1 giường đôi Queen hoặc 2 giường đơn',
            'area'                => 32,
            'max_guests'          => 2,
            'max_children'        => 2,
            'quantity'            => 80,
            'price'               => 1400000,
            'is_refundable'       => 1,
            'is_sale'             => 0,
            'room_badge'          => 'Best Seller',
            'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
            'room_amenities'      => $qaStd,
            'benefits'            => json_encode(['View biển Trần Phú trực diện', 'Bữa sáng buffet', 'Bồn tắm nằm', 'Điều hòa trung tâm']),
            'room_notes'          => json_encode('Phòng 32m² 100% hướng biển, sàn gỗ, TV LED 40 inch, bồn tắm nằm và buồng tắm đứng. Nội thất Art Deco châu Âu.'),
        ]);

        // Room 9 — Family Suite Ocean View Balcony (cập nhật từ Suite VIP)
        DB::table('rooms')->where('id', 9)->update([
            'room_name'           => 'Family Suite Ocean View Balcony',
            'package_name'        => 'Family Suite - 3 khu riêng biệt + View biển',
            'bed_type'            => '1 giường đôi King + 1 giường đơn',
            'area'                => 65,
            'max_guests'          => 4,
            'max_children'        => 2,
            'quantity'            => 20,
            'price'               => 3200000,
            'is_refundable'       => 1,
            'is_sale'             => 0,
            'room_badge'          => 'Gia đình',
            'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
            'room_amenities'      => $qaSuite,
            'benefits'            => json_encode(['Phòng ngủ + Phòng khách + Quầy bar riêng biệt', 'Ban công hướng biển', 'Phù hợp gia đình 3-4 người', 'Bữa sáng buffet']),
            'room_notes'          => json_encode('Suite 65m² thiết kế 3 khu tách biệt: phòng ngủ, phòng khách và quầy bar riêng. Ban công hướng biển Trần Phú.'),
        ]);

        // Room 10 — Studio Deluxe (cập nhật từ Studio Heritage)
        DB::table('rooms')->where('id', 10)->update([
            'room_name'           => 'Studio Deluxe',
            'package_name'        => 'Studio Deluxe - Bếp nhỏ tiện nghi',
            'bed_type'            => '1 giường đôi Queen',
            'area'                => 32,
            'max_guests'          => 2,
            'max_children'        => 1,
            'quantity'            => 80,
            'price'               => 1550000,
            'is_refundable'       => 1,
            'is_sale'             => 1,
            'room_badge'          => 'Phổ biến',
            'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
            'room_amenities'      => $citStudio,
            'benefits'            => json_encode(['Bếp nhỏ (kitchenette)', 'Gần Vincom & Phố Tây', 'Hồ bơi tầng 3', 'Bãi đậu xe miễn phí']),
            'room_notes'          => json_encode('Studio 32m² với bếp nhỏ tiện nghi, phù hợp cặp đôi hoặc khách công tác lưu trú ngắn ngày.'),
        ]);

        // Room 11 — One-Bedroom Executive (cập nhật từ Căn hộ 2 PN)
        DB::table('rooms')->where('id', 11)->update([
            'room_name'           => 'One-Bedroom Executive',
            'package_name'        => 'One-Bedroom Executive - Tầng cao view biển',
            'bed_type'            => '1 giường đôi King',
            'area'                => 70,
            'max_guests'          => 3,
            'max_children'        => 1,
            'quantity'            => 20,
            'price'               => 4000000,
            'is_refundable'       => 1,
            'is_sale'             => 0,
            'room_badge'          => 'Cao cấp nhất',
            'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
            'room_amenities'      => $citApt,
            'benefits'            => json_encode(['Tầng cao - view biển đỉnh', 'Phòng khách rộng', 'Bếp đầy đủ + Máy giặt sấy', 'Ban công lớn hướng biển']),
            'room_notes'          => json_encode('Căn hộ 1 phòng ngủ 70m² hạng cao cấp nhất tại Citadines, tầng cao với ban công lớn nhìn thẳng ra biển Trần Phú.'),
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 3. THÊM CÁC LOẠI PHÒNG MỚI
        // ══════════════════════════════════════════════════════════════════

        // ── Amiana Resort — 3 loại phòng mới ─────────────────────────────
        DB::table('rooms')->insert([
            [
                'hotel_id'            => 4,
                'room_name'           => 'Deluxe Garden View',
                'package_name'        => 'Deluxe Garden View - Vật liệu địa phương đặc trưng',
                'bed_type'            => '1 giường đôi King hoặc 2 giường Twin',
                'price'               => 5500000,
                'quantity'            => 60,
                'max_guests'          => 2,
                'max_children'        => 1,
                'area'                => 65,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'Mới',
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'room_amenities'      => $amDeluxe,
                'benefits'            => json_encode(['Nội thất tre & gốm Bát Tràng', 'View vườn nhiệt đới', 'Bữa sáng cho 2', 'Hồ bơi 2.500m²']),
                'room_notes'          => json_encode('Phòng 65m² với nội thất từ vật liệu địa phương (tre, gốm Bát Tràng, đá, vỏ dừa). View vườn xanh mát và yên tĩnh.'),
                'image'               => null,
            ],
            [
                'hotel_id'            => 4,
                'room_name'           => 'Ocean View Room',
                'package_name'        => 'Ocean View Room - Tầm nhìn trực diện Vịnh Nha Trang',
                'bed_type'            => '1 giường đôi King',
                'price'               => 7200000,
                'quantity'            => 50,
                'max_guests'          => 2,
                'max_children'        => 1,
                'area'                => 70,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'View biển',
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'room_amenities'      => json_encode(['breakfast','wifi','ac','tv','minibar','safe','desk',
                                                      'bathtub','shower_standing','private_bathroom',
                                                      'hair_dryer','bathrobe','toiletries','hot_water',
                                                      'free_bottled_water','non_smoking']),
                'benefits'            => json_encode(['Tầm nhìn biển toàn cảnh', 'Bồn tắm hướng biển', 'Ban công riêng rộng', 'Bữa sáng cho 2']),
                'room_notes'          => json_encode('Phòng 70m² với tầm nhìn trực diện Vịnh Nha Trang, bồn tắm đặt hướng ra biển, ban công rộng rãi. Lý tưởng cho kỳ nghỉ lãng mạn.'),
                'image'               => null,
            ],
            [
                'hotel_id'            => 4,
                'room_name'           => 'Beachfront Villa with Private Pool',
                'package_name'        => 'Beachfront Villa - Bãi biển riêng + Hồ bơi riêng',
                'bed_type'            => '1 giường đôi King + Phòng ngủ phụ',
                'price'               => 35000000,
                'quantity'            => 10,
                'max_guests'          => 4,
                'max_children'        => 2,
                'area'                => 450,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'Ultra Luxury',
                'cancellation_policy' => 'Hủy miễn phí trước 7 ngày nhận phòng.',
                'room_amenities'      => json_encode(['breakfast','wifi','ac','tv','minibar','fridge','safe','desk',
                                                      'bathtub','shower_standing','private_bathroom',
                                                      'hair_dryer','bathrobe','toiletries','hot_water',
                                                      'free_bottled_water','waiting_area','non_smoking']),
                'benefits'            => json_encode(['Hồ bơi riêng lớn', 'Bãi biển riêng trực tiếp', '2 phòng ngủ', 'Nhà bếp đầy đủ', 'Butler 24h', 'Bữa sáng riêng phòng']),
                'room_notes'          => json_encode('Biệt thự 450m² sang trọng bậc nhất, trực diện bãi biển riêng với hồ bơi ngoài trời. 2 phòng ngủ, phòng khách, nhà bếp đầy đủ và butler phục vụ 24h.'),
                'image'               => null,
            ],
        ]);

        // ── Citadines — 5 loại phòng mới ─────────────────────────────────
        DB::table('rooms')->insert([
            [
                'hotel_id'            => 6,
                'room_name'           => 'Studio Twin',
                'package_name'        => 'Studio Twin - 2 giường đơn riêng biệt',
                'bed_type'            => '2 giường đơn Twin',
                'price'               => 1550000,
                'quantity'            => 60,
                'max_guests'          => 2,
                'max_children'        => 1,
                'area'                => 32,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => null,
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'room_amenities'      => $citStudio,
                'benefits'            => json_encode(['2 giường đơn riêng biệt', 'Bếp nhỏ', 'Hồ bơi tầng 3', 'Phù hợp bạn bè đồng hành']),
                'room_notes'          => json_encode('Studio 32m² với 2 giường đơn, thích hợp cho bạn bè hoặc đồng nghiệp đi cùng. View thành phố Nha Trang.'),
                'image'               => null,
            ],
            [
                'hotel_id'            => 6,
                'room_name'           => 'Studio Deluxe Twin',
                'package_name'        => 'Studio Deluxe Twin - Ban công hướng biển',
                'bed_type'            => '2 giường đơn Twin',
                'price'               => 1750000,
                'quantity'            => 50,
                'max_guests'          => 2,
                'max_children'        => 1,
                'area'                => 35,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'Phổ biến',
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'room_amenities'      => $citStudio,
                'benefits'            => json_encode(['Ban công hướng biển', 'Rộng hơn Studio thường', 'Bếp nhỏ', 'Hồ bơi tầng 3']),
                'room_notes'          => json_encode('Studio 35m² rộng hơn với ban công có tầm nhìn ra biển hoặc thành phố. 2 giường đơn linh hoạt.'),
                'image'               => null,
            ],
            [
                'hotel_id'            => 6,
                'room_name'           => 'Studio Executive',
                'package_name'        => 'Studio Executive - Tầng cao view biển',
                'bed_type'            => '1 giường đôi King',
                'price'               => 2200000,
                'quantity'            => 40,
                'max_guests'          => 2,
                'max_children'        => 1,
                'area'                => 40,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'Best Seller',
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'room_amenities'      => json_encode(['wifi','ac','tv','fridge','safe','shower_standing',
                                                      'private_bathroom','hair_dryer','toiletries','hot_water',
                                                      'free_bottled_water','blackout_curtains']),
                'benefits'            => json_encode(['Tầng cao view biển tốt hơn', 'Ban công hướng biển', 'Bếp đầy đủ + Máy giặt', 'Phòng khách nhỏ']),
                'room_notes'          => json_encode('Studio 40m² tầng cao với ban công hướng biển trực tiếp, bếp đầy đủ và máy giặt. Lý tưởng cho lưu trú 3-7 ngày.'),
                'image'               => null,
            ],
            [
                'hotel_id'            => 6,
                'room_name'           => 'Studio Premier Twin',
                'package_name'        => 'Studio Premier Twin - Biển toàn cảnh cao tầng',
                'bed_type'            => '2 giường đơn Twin',
                'price'               => 2500000,
                'quantity'            => 30,
                'max_guests'          => 2,
                'max_children'        => 1,
                'area'                => 42,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'View biển',
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'room_amenities'      => json_encode(['wifi','ac','tv','fridge','safe','shower_standing',
                                                      'private_bathroom','hair_dryer','toiletries','hot_water',
                                                      'free_bottled_water','blackout_curtains']),
                'benefits'            => json_encode(['Biển toàn cảnh từ tầng cao', 'Ban công riêng rộng', 'Bếp đầy đủ + Máy giặt', '2 giường đơn']),
                'room_notes'          => json_encode('Studio Premier 42m² tầng cao nhất với tầm nhìn toàn cảnh biển Nha Trang và ban công rộng. 2 giường đơn riêng biệt.'),
                'image'               => null,
            ],
            [
                'hotel_id'            => 6,
                'room_name'           => 'One-Bedroom Deluxe',
                'package_name'        => 'One-Bedroom Deluxe - Phòng ngủ tách biệt phòng khách',
                'bed_type'            => '1 phòng ngủ riêng (giường King) + phòng khách',
                'price'               => 3200000,
                'quantity'            => 30,
                'max_guests'          => 3,
                'max_children'        => 1,
                'area'                => 60,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'Gia đình',
                'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                'room_amenities'      => $citApt,
                'benefits'            => json_encode(['Phòng ngủ tách biệt phòng khách', 'Bếp đầy đủ + Bàn ăn', 'Máy giặt', '2 TV', 'Lý tưởng lưu trú dài ngày']),
                'room_notes'          => json_encode('Căn hộ 1 phòng ngủ 60m² với phòng ngủ riêng tách biệt, phòng khách, bếp đầy đủ và máy giặt. Phù hợp gia đình nhỏ hoặc lưu trú dài ngày.'),
                'image'               => null,
            ],
        ]);

        // ── Queen Ann — 5 loại phòng mới ─────────────────────────────────
        DB::table('rooms')->insert([
            [
                'hotel_id'            => 5,
                'room_name'           => 'Superior',
                'package_name'        => 'Superior - Giá tốt nhất + Bữa sáng',
                'bed_type'            => '1 giường đôi hoặc 2 giường đơn',
                'price'               => 1242000,
                'quantity'            => 60,
                'max_guests'          => 2,
                'max_children'        => 1,
                'area'                => 32,
                'is_refundable'       => 1,
                'is_sale'             => 1,
                'is_tax_included'     => 1,
                'room_badge'          => 'Giá tốt',
                'cancellation_policy' => 'Hủy miễn phí trước 24 giờ nhận phòng.',
                'room_amenities'      => $qaStd,
                'benefits'            => json_encode(['Giá tốt nhất tại Queen Ann', 'Bữa sáng buffet', 'Sàn gỗ cao cấp', 'Sofa thư giãn']),
                'room_notes'          => json_encode('Phòng Superior 32m² view góc thành phố và một phần biển. Sàn gỗ, TV LED 40 inch, bồn tắm nằm và sofa thư giãn.'),
                'image'               => null,
            ],
            [
                'hotel_id'            => 5,
                'room_name'           => 'Premier Twin Ocean View Balcony',
                'package_name'        => 'Premier Twin - Ban công ngắm biển',
                'bed_type'            => '2 giường đơn Twin',
                'price'               => 1800000,
                'quantity'            => 40,
                'max_guests'          => 2,
                'max_children'        => 1,
                'area'                => 35,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'Phổ biến',
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'room_amenities'      => $qaStd,
                'benefits'            => json_encode(['Ban công riêng hướng biển', 'View biển toàn cảnh', 'Bữa sáng buffet', '2 giường đơn linh hoạt']),
                'room_notes'          => json_encode('Phòng Premier 35m² với ban công ngắm biển, view biển toàn cảnh Trần Phú. Sàn gỗ, bồn tắm nằm cao cấp.'),
                'image'               => null,
            ],
            [
                'hotel_id'            => 5,
                'room_name'           => 'Premier Queen Ocean View Balcony',
                'package_name'        => 'Premier Queen - Lãng mạn hướng biển',
                'bed_type'            => '1 giường đôi King',
                'price'               => 1900000,
                'quantity'            => 40,
                'max_guests'          => 2,
                'max_children'        => 1,
                'area'                => 35,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'Cặp đôi',
                'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                'room_amenities'      => $qaStd,
                'benefits'            => json_encode(['Ban công hướng biển', 'Giường King lãng mạn', 'View biển toàn cảnh', 'Bữa sáng buffet']),
                'room_notes'          => json_encode('Phòng Premier 35m² với giường King và ban công hướng biển, lý tưởng cho cặp đôi. Sàn gỗ ấm cúng, bồn tắm nằm.'),
                'image'               => null,
            ],
            [
                'hotel_id'            => 5,
                'room_name'           => 'Suite Triple Balcony',
                'package_name'        => 'Suite Triple - Nhóm bạn / Công tác',
                'bed_type'            => '3 giường đơn',
                'price'               => 3500000,
                'quantity'            => 15,
                'max_guests'          => 3,
                'max_children'        => 1,
                'area'                => 70,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'Nhóm bạn',
                'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                'room_amenities'      => $qaSuite,
                'benefits'            => json_encode(['3 giường đơn riêng biệt', 'Phòng khách + Quầy bar', 'Ban công hướng biển', 'Bữa sáng buffet']),
                'room_notes'          => json_encode('Suite 70m² với 3 giường đơn, phòng khách riêng và quầy bar. Được ưa chuộng bởi nhóm bạn đi du lịch hoặc khách công tác.'),
                'image'               => null,
            ],
            [
                'hotel_id'            => 5,
                'room_name'           => 'Queen Ann Suite',
                'package_name'        => 'Queen Ann Suite - Đẳng cấp Nữ Hoàng',
                'bed_type'            => '1 giường đôi King cỡ lớn',
                'price'               => 4840000,
                'quantity'            => 10,
                'max_guests'          => 2,
                'max_children'        => 0,
                'area'                => null,
                'is_refundable'       => 1,
                'is_sale'             => 0,
                'is_tax_included'     => 1,
                'room_badge'          => 'VIP',
                'cancellation_policy' => 'Hủy miễn phí trước 5 ngày nhận phòng.',
                'room_amenities'      => json_encode(['breakfast','wifi','ac','tv','minibar','fridge','safe','desk',
                                                      'bathtub','shower_standing','private_bathroom',
                                                      'hair_dryer','bathrobe','toiletries','hot_water',
                                                      'free_bottled_water','waiting_area','non_smoking']),
                'benefits'            => json_encode(['Phòng được săn đón nhất Queen Ann', 'Thiết kế lấy cảm hứng từ Nữ Hoàng', 'Tầm nhìn biển trực diện', 'Butler service', 'Bữa sáng riêng phòng']),
                'room_notes'          => json_encode('Queen Ann Suite hạng cao cấp nhất, thiết kế lấy cảm hứng từ phòng Nữ Hoàng với nội thất xa hoa từng chi tiết. Bồn tắm đẳng cấp, tầm nhìn biển trực diện và butler service.'),
                'image'               => null,
            ],
        ]);
    }

    public function down(): void
    {
        // Khôi phục hotels về trạng thái trước
        DB::table('hotels')->where('id', 4)->update(['stars' => 0, 'review_count' => '538', 'rating' => 9.1]);
        DB::table('hotels')->where('id', 5)->update(['stars' => 0, 'rating' => 8.3, 'review_count' => '321', 'price' => 1400000]);
        DB::table('hotels')->where('id', 6)->update(['rating' => 8.5, 'review_count' => '274']);

        // Khôi phục rooms 6-11
        DB::table('rooms')->where('id', 6)->update(['room_name' => 'Ocean / Garden Villa', 'area' => 55, 'price' => 4800000]);
        DB::table('rooms')->where('id', 7)->update(['room_name' => 'Pool Villa (Hồ bơi riêng)', 'area' => 120, 'price' => 9500000]);
        DB::table('rooms')->where('id', 8)->update(['room_name' => 'Deluxe / Executive', 'area' => 28, 'price' => 1400000]);
        DB::table('rooms')->where('id', 9)->update(['room_name' => 'Suite (VIP)', 'area' => 55, 'price' => 3200000]);
        DB::table('rooms')->where('id', 10)->update(['room_name' => 'Studio Heritage', 'area' => 30, 'price' => 1550000]);
        DB::table('rooms')->where('id', 11)->update(['room_name' => 'Căn hộ 2 PN', 'area' => 65, 'price' => 4100000]);

        // Xóa rooms mới đã thêm
        DB::table('rooms')->where('hotel_id', 4)->whereIn('room_name', [
            'Deluxe Garden View', 'Ocean View Room', 'Beachfront Villa with Private Pool',
        ])->delete();
        DB::table('rooms')->where('hotel_id', 6)->whereIn('room_name', [
            'Studio Twin', 'Studio Deluxe Twin', 'Studio Executive', 'Studio Premier Twin', 'One-Bedroom Deluxe',
        ])->delete();
        DB::table('rooms')->where('hotel_id', 5)->whereIn('room_name', [
            'Superior', 'Premier Twin Ocean View Balcony', 'Premier Queen Ocean View Balcony',
            'Suite Triple Balcony', 'Queen Ann Suite',
        ])->delete();
    }
};
