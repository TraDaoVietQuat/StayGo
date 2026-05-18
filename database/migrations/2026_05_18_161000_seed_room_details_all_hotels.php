<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── TIỆN ÍCH PHÒNG theo phân khúc ────────────────────────────────
        $base5star = ['wifi','ac','tv','minibar','fridge','safe','desk','bathtub',
                      'shower_standing','private_bathroom','hair_dryer','bathrobe',
                      'toiletries','hot_water','free_bottled_water'];
        $base4star = ['wifi','ac','tv','fridge','safe','desk','shower_standing',
                      'private_bathroom','hair_dryer','toiletries','hot_water','free_bottled_water'];
        $base3star = ['wifi','ac','tv','fridge','safe','desk','shower_standing',
                      'private_bathroom','hair_dryer','toiletries','hot_water'];
        $withWaiting   = array_merge($base5star, ['waiting_area']);
        $withNonSmoke  = array_merge($base5star, ['non_smoking']);
        $withAll5star  = array_merge($base5star, ['waiting_area','non_smoking']);
        $base4Deluxe   = array_merge($base4star, ['bathtub','bathrobe']);
        $base4Suite    = array_merge($base4star, ['minibar','bathtub','bathrobe','waiting_area']);
        $base3Deluxe   = array_merge($base3star, ['blackout_curtains','free_bottled_water']);

        // Cập nhật theo hotel_id + room_name
        $updates = [

            // ── Hotel 1: Ana Mandara Villas Dalat Resort & Spa ────────────
            [
                'hotel_id'  => 1,
                'room_name' => 'Phòng Villa',
                'data'      => [
                    'package_name'        => 'Villa phong cách Pháp - View vườn',
                    'room_badge'          => 'Phổ biến',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base5star),
                    'benefits'            => json_encode(['View vườn xanh mát', 'Bồn tắm cao cấp', 'Nội thất cổ điển Pháp']),
                    'room_notes'          => json_encode('Villa 28m² phong cách Pháp cổ điển, view vườn nhiệt đới yên tĩnh.'),
                ],
            ],
            [
                'hotel_id'  => 1,
                'room_name' => 'Biệt Thự Với Studio Vườn',
                'data'      => [
                    'package_name'        => 'Biệt thự Studio Vườn - Không gian riêng tư',
                    'room_badge'          => 'Best Seller',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($withWaiting),
                    'benefits'            => json_encode(['Bếp nhỏ tiện nghi', 'Phòng khách riêng', 'View vườn thư giãn']),
                    'room_notes'          => json_encode('Biệt thự studio 40m² với phòng khách, góc bếp nhỏ và view vườn tuyệt đẹp.'),
                ],
            ],
            [
                'hotel_id'  => 1,
                'room_name' => 'Biệt Thự Suite Vườn',
                'data'      => [
                    'package_name'        => 'Biệt thự Suite Vườn cao cấp',
                    'room_badge'          => 'Sang trọng',
                    'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($withAll5star),
                    'benefits'            => json_encode(['Biệt thự riêng biệt', 'View vườn đặc biệt', 'Hỗ trợ butler riêng']),
                    'room_notes'          => json_encode('Suite 52m² với phòng ngủ và phòng khách tách biệt, view vườn nhiệt đới.'),
                ],
            ],

            // ── Hotel 2: Colline Hotel ─────────────────────────────────────
            [
                'hotel_id'  => 2,
                'room_name' => 'Suite Signature Với Cảnh Thành Phố Và Hồ Quang Cảnh Thành Phố',
                'data'      => [
                    'package_name'        => 'Suite Signature - View thành phố & hồ',
                    'room_badge'          => 'VIP',
                    'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($withAll5star),
                    'benefits'            => json_encode(['View hồ và thành phố', 'Phòng khách riêng', 'Minibar đầy đủ']),
                    'room_notes'          => json_encode('Suite 55m² sang trọng với tầm nhìn 180° ra hồ và toàn cảnh thành phố Đà Lạt.'),
                ],
            ],
            [
                'hotel_id'  => 2,
                'room_name' => 'Phòng Đôi Cao Cấp Không Có Cửa Sổ',
                'data'      => [
                    'package_name'        => 'Phòng đôi cao cấp - Không có cửa sổ',
                    'room_badge'          => 'Tiết kiệm',
                    'cancellation_policy' => 'Hủy miễn phí trước 24 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base4star),
                    'benefits'            => json_encode(['Giá tốt nhất', 'Tiện nghi đầy đủ', 'Nội thất hiện đại']),
                    'room_notes'          => json_encode('Phòng đôi 22m² tiện nghi, phù hợp lưu trú ngắn ngày.'),
                ],
            ],
            [
                'hotel_id'  => 2,
                'room_name' => 'Phòng Đôi Sang Trọng Nhìn Ra Thành Phố Quang Cảnh Thành Phố',
                'data'      => [
                    'package_name'        => 'Phòng đôi sang trọng - View thành phố',
                    'room_badge'          => 'Phổ biến',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base4Deluxe),
                    'benefits'            => json_encode(['Tầm nhìn thành phố Đà Lạt', 'Bồn tắm', '2 giường đơn linh hoạt']),
                    'room_notes'          => json_encode('Phòng 25m² view thành phố Đà Lạt, phù hợp cho cặp đôi hoặc 2 người đi cùng.'),
                ],
            ],

            // ── Hotel 3: New Life Hotel Đà Lạt ────────────────────────────
            [
                'hotel_id'  => 3,
                'room_name' => 'Phòng Đôi Tiêu Chuẩn',
                'data'      => [
                    'package_name'        => 'Phòng tiêu chuẩn - Giá tốt nhất',
                    'room_badge'          => 'Giá tốt',
                    'cancellation_policy' => 'Hủy miễn phí trước 24 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base3star),
                    'benefits'            => json_encode(['Giá phải chăng', 'Trung tâm thành phố', 'Wi-Fi miễn phí']),
                    'room_notes'          => json_encode('Phòng tiêu chuẩn 17m², đầy đủ tiện nghi cơ bản, vị trí trung tâm Đà Lạt.'),
                ],
            ],
            [
                'hotel_id'  => 3,
                'room_name' => 'Phòng Đôi Sang Trọng Nhìn Ra Phố Quang Cảnh Đường Phố',
                'data'      => [
                    'package_name'        => 'Phòng sang trọng - View phố Đà Lạt',
                    'room_badge'          => 'Phổ biến',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base3Deluxe),
                    'benefits'            => json_encode(['View phố Đà Lạt', 'Giường Queen thoải mái', 'Phòng rộng rãi hơn']),
                    'room_notes'          => json_encode('Phòng 25m² view đường phố Đà Lạt, nội thất sang trọng hơn phòng tiêu chuẩn.'),
                ],
            ],

            // ── Hotel 7: The Imperial Vung Tau Hotel & Resort ─────────────
            [
                'hotel_id'  => 7,
                'room_name' => 'Heritage Deluxe King Hồ Bơi Hoặc Vườn',
                'data'      => [
                    'package_name'        => 'Heritage Deluxe King - Hồ bơi/Vườn',
                    'room_badge'          => 'Phổ biến',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base5star),
                    'benefits'            => json_encode(['View hồ bơi hoặc vườn', 'Giường King rộng rãi', 'Bồn tắm cao cấp']),
                    'room_notes'          => json_encode('Phòng Heritage Deluxe 40m² với giường King, view hồ bơi hoặc vườn nhiệt đới.'),
                ],
            ],
            [
                'hotel_id'  => 7,
                'room_name' => 'Phòng Heritage Deluxe Twin Hồ Bơi Hoặc Vườn',
                'data'      => [
                    'package_name'        => 'Heritage Deluxe Twin - Hồ bơi/Vườn',
                    'room_badge'          => null,
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base5star),
                    'benefits'            => json_encode(['2 giường đơn linh hoạt', 'View hồ bơi hoặc vườn', 'Bồn tắm cao cấp']),
                    'room_notes'          => json_encode('Phòng Heritage Deluxe 40m² với 2 giường đơn, view hồ bơi hoặc vườn.'),
                ],
            ],
            [
                'hotel_id'  => 7,
                'room_name' => 'Phòng Residence Twin Hồ Bơi Hoặc Vườn',
                'data'      => [
                    'package_name'        => 'Residence Twin - Không gian rộng hơn',
                    'room_badge'          => 'Sang trọng',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($withWaiting),
                    'benefits'            => json_encode(['Phòng rộng 46m²', 'Khu vực phòng khách', 'View hồ bơi/vườn']),
                    'room_notes'          => json_encode('Phòng Residence 46m² với khu vực ngồi riêng, rộng rãi và thoáng đãng.'),
                ],
            ],
            [
                'hotel_id'  => 7,
                'room_name' => 'Residence King Ocean Biển',
                'data'      => [
                    'package_name'        => 'Residence King Ocean - View biển trực tiếp',
                    'room_badge'          => 'View biển',
                    'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($withAll5star),
                    'benefits'            => json_encode(['View biển Vũng Tàu trực tiếp', 'Phòng khách riêng', 'Giường King cao cấp']),
                    'room_notes'          => json_encode('Phòng Residence 46m² hướng biển, tầm nhìn trực tiếp ra biển Vũng Tàu.'),
                ],
            ],
            [
                'hotel_id'  => 7,
                'room_name' => 'Phòng Ba Người Truyền Thống Hồ Bơi Hoặc Vườn',
                'data'      => [
                    'package_name'        => 'Phòng 3 người - Lý tưởng cho gia đình',
                    'room_badge'          => 'Gia đình',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode(array_merge($base5star, ['non_smoking'])),
                    'benefits'            => json_encode(['Dành cho 3 người', 'Bồn tắm riêng', 'View hồ bơi/vườn']),
                    'room_notes'          => json_encode('Phòng 45m² thiết kế cho 3 người, với 1 giường đôi và 1 giường đơn.'),
                ],
            ],

            // ── Hotel 8: Premier Pearl Hotel Vũng Tàu ─────────────────────
            [
                'hotel_id'  => 8,
                'room_name' => 'Phòng Twin Cổ Điển_không Có Tầm Nhìn',
                'data'      => [
                    'package_name'        => 'Classic Twin - Không có tầm nhìn',
                    'room_badge'          => 'Giá tốt',
                    'cancellation_policy' => 'Hủy miễn phí trước 24 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base4star),
                    'benefits'            => json_encode(['Giá tiết kiệm nhất', 'Tiện nghi đầy đủ', '2 giường đơn']),
                    'room_notes'          => json_encode('Phòng 22m² với 2 giường đơn, không có cửa sổ nhìn ra ngoài.'),
                ],
            ],
            [
                'hotel_id'  => 8,
                'room_name' => 'Phòng Đôi Cổ Điển Nhìn Một Phần',
                'data'      => [
                    'package_name'        => 'Classic Double - Nhìn một phần biển',
                    'room_badge'          => null,
                    'cancellation_policy' => 'Hủy miễn phí trước 24 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base4star),
                    'benefits'            => json_encode(['Nhìn một phần ra biển', 'Giường đôi thoải mái', 'Tiện nghi đầy đủ']),
                    'room_notes'          => json_encode('Phòng 22m² với tầm nhìn một phần ra biển Vũng Tàu.'),
                ],
            ],
            [
                'hotel_id'  => 8,
                'room_name' => 'Phòng Classic Twin Hướng Biển',
                'data'      => [
                    'package_name'        => 'Classic Twin - Hướng biển',
                    'room_badge'          => 'Phổ biến',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base4star),
                    'benefits'            => json_encode(['View biển trực tiếp', '2 giường đơn linh hoạt', 'Tầng cao']),
                    'room_notes'          => json_encode('Phòng 22m² với 2 giường đơn, hướng thẳng ra biển Vũng Tàu.'),
                ],
            ],
            [
                'hotel_id'  => 8,
                'room_name' => 'Phòng Đôi Cổ Điển Nhìn Ra Biển',
                'data'      => [
                    'package_name'        => 'Classic Double - Nhìn ra biển',
                    'room_badge'          => 'Phổ biến',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base4star),
                    'benefits'            => json_encode(['View biển đẹp', 'Giường đôi cao cấp', 'Phòng yên tĩnh']),
                    'room_notes'          => json_encode('Phòng 22m² với giường đôi, view biển toàn cảnh từ tầng cao.'),
                ],
            ],
            [
                'hotel_id'  => 8,
                'room_name' => 'Signature Suite',
                'data'      => [
                    'package_name'        => 'Signature Suite - Không gian sang trọng',
                    'room_badge'          => 'Sang trọng',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base4Suite),
                    'benefits'            => json_encode(['Phòng khách riêng', 'Bồn tắm cao cấp', 'Minibar đầy đủ']),
                    'room_notes'          => json_encode('Suite 35m² sang trọng với phòng khách và bồn tắm riêng biệt.'),
                ],
            ],
            [
                'hotel_id'  => 8,
                'room_name' => 'Suite Signature Hướng Biển',
                'data'      => [
                    'package_name'        => 'Signature Suite - Hướng biển',
                    'room_badge'          => 'Best Seller',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base4Suite),
                    'benefits'            => json_encode(['View biển tuyệt đẹp', 'Phòng khách riêng', 'Bồn tắm & minibar']),
                    'room_notes'          => json_encode('Suite 35m² hướng biển, lý tưởng cho cặp đôi nghỉ dưỡng lãng mạn.'),
                ],
            ],
            [
                'hotel_id'  => 8,
                'room_name' => 'Phòng 3 Người Lớn Sang Trọng Nhìn Ra Biển',
                'data'      => [
                    'package_name'        => 'Phòng 3 người - Sang trọng hướng biển',
                    'room_badge'          => 'Gia đình',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base4Deluxe),
                    'benefits'            => json_encode(['Phù hợp 3 người', 'View biển', 'Bồn tắm cao cấp']),
                    'room_notes'          => json_encode('Phòng 28m² cho 3 người với giường đôi và giường lớn, view biển.'),
                ],
            ],
            [
                'hotel_id'  => 8,
                'room_name' => 'Phòng Premier Suite Hướng Biển Oceanfront',
                'data'      => [
                    'package_name'        => 'Premier Suite - Oceanfront cao cấp',
                    'room_badge'          => 'VIP',
                    'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode(array_merge($base4Suite, ['non_smoking'])),
                    'benefits'            => json_encode(['Oceanfront trực tiếp', 'Phòng khách rộng', 'Bồn tắm & minibar']),
                    'room_notes'          => json_encode('Premier Suite 40m² sát mặt biển, tầm nhìn panorama ra vịnh Vũng Tàu.'),
                ],
            ],
            [
                'hotel_id'  => 8,
                'room_name' => 'Phòng Signature Suite Hướng Biển',
                'data'      => [
                    'package_name'        => 'Signature Suite Hướng Biển - Rộng 45m²',
                    'room_badge'          => 'VIP',
                    'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode(array_merge($base4Suite, ['non_smoking'])),
                    'benefits'            => json_encode(['View biển 180°', 'Phòng rộng nhất hạng suite', 'Butler riêng']),
                    'room_notes'          => json_encode('Signature Suite 45m² hướng biển, không gian rộng rãi với bồn tắm và khu vực ngồi.'),
                ],
            ],
            [
                'hotel_id'  => 8,
                'room_name' => 'Phòng Thông Nhau Với Tầm Nhìn Ra Biển',
                'data'      => [
                    'package_name'        => 'Phòng thông nhau - Lý tưởng gia đình lớn',
                    'room_badge'          => 'Gia đình',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode(array_merge($base4Suite, ['non_smoking'])),
                    'benefits'            => json_encode(['2 phòng thông nhau', 'View biển', 'Phù hợp gia đình 4-5 người']),
                    'room_notes'          => json_encode('2 phòng thông nhau 45m² với view biển, lý tưởng cho gia đình hoặc nhóm bạn.'),
                ],
            ],
            [
                'hotel_id'  => 8,
                'room_name' => 'Phòng Tổng Thống',
                'data'      => [
                    'package_name'        => 'Presidential Suite - Đẳng cấp nhất',
                    'room_badge'          => 'Presidential',
                    'cancellation_policy' => 'Hủy miễn phí trước 5 ngày nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode(array_merge($base5star, ['waiting_area','non_smoking','breakfast'])),
                    'benefits'            => json_encode(['Phòng tổng thống 180m²', 'Trọn gói dịch vụ VIP', 'Butler 24/7', 'Bữa sáng riêng']),
                    'room_notes'          => json_encode('Presidential Suite 180m² - đẳng cấp nhất của Premier Pearl với dịch vụ butler 24/7.'),
                ],
            ],

            // ── Hotel 9: Marina Bay Vung Tau Resort ───────────────────────
            [
                'hotel_id'  => 9,
                'room_name' => 'Phòng Premier Đôi Hướng Vườn Hướng Ra Vườn',
                'data'      => [
                    'package_name'        => 'Premier Double - Hướng vườn yên tĩnh',
                    'room_badge'          => 'Phổ biến',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode(array_merge($base4star, ['blackout_curtains'])),
                    'benefits'            => json_encode(['View vườn yên tĩnh', '2 giường đơn', 'Gần hồ bơi']),
                    'room_notes'          => json_encode('Phòng Premier 40m² với 2 giường đơn, view vườn nhiệt đới thoáng đãng.'),
                ],
            ],
            [
                'hotel_id'  => 9,
                'room_name' => 'Phòng Premier Twin Nhìn Ra Biển',
                'data'      => [
                    'package_name'        => 'Premier Twin - View biển trực tiếp',
                    'room_badge'          => 'Best Seller',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode(array_merge($base4star, ['blackout_curtains'])),
                    'benefits'            => json_encode(['View biển đẹp', '2 giường đơn', 'Ban công riêng']),
                    'room_notes'          => json_encode('Phòng Premier Twin 40m² với tầm nhìn trực tiếp ra biển Vũng Tàu.'),
                ],
            ],
            [
                'hotel_id'  => 9,
                'room_name' => 'Phòng Premier Double Ocean View Hướng Nhìn Ra Biển',
                'data'      => [
                    'package_name'        => 'Premier Double Ocean View - Lãng mạn',
                    'room_badge'          => 'View biển',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode(array_merge($base4star, ['blackout_curtains'])),
                    'benefits'            => json_encode(['View biển panorama', 'Giường lớn lãng mạn', 'Ban công riêng']),
                    'room_notes'          => json_encode('Phòng Premier 40m² giường lớn, view biển toàn cảnh, lý tưởng cho cặp đôi.'),
                ],
            ],

            // ── Hotel 10: InterContinental Danang ─────────────────────────
            [
                'hotel_id'  => 10,
                'room_name' => '1 King Classic Panoramic Ocean View Ocean View',
                'data'      => [
                    'bed_type'            => '1 giường cỡ King',
                    'package_name'        => 'King Classic - Panoramic Ocean View',
                    'room_badge'          => 'Phổ biến',
                    'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($withAll5star),
                    'benefits'            => json_encode(['Tầm nhìn biển 180°', 'Giường King sang trọng', 'Bồn tắm và bồn tắm đứng']),
                    'room_notes'          => json_encode('Phòng Classic 70m² với giường King, tầm nhìn panorama ra vịnh Đà Nẵng từ ban công riêng.'),
                ],
            ],
            [
                'hotel_id'  => 10,
                'room_name' => '1 King Terrace Suite Ocean View Ocean View',
                'data'      => [
                    'package_name'        => 'King Terrace Suite - Sân thượng riêng',
                    'room_badge'          => 'Best Seller',
                    'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode(array_merge($withAll5star, ['breakfast'])),
                    'benefits'            => json_encode(['Sân thượng riêng tư', 'View biển không cản', 'Bữa sáng included', 'Butler riêng']),
                    'room_notes'          => json_encode('Terrace Suite 80m² với sân thượng riêng nhìn ra biển Đà Nẵng, đẳng cấp 5 sao quốc tế.'),
                ],
            ],

            // ── Hotel 11: Novotel Danang Premier Han River ────────────────
            [
                'hotel_id'  => 11,
                'room_name' => 'Phòng Superior, Ban Công Với Tầm Nhìn Ra Sông Hàn - 2 Giường Đơn',
                'data'      => [
                    'area'                => 35,
                    'package_name'        => 'Superior Twin - Ban công view sông Hàn',
                    'room_badge'          => 'Phổ biến',
                    'cancellation_policy' => 'Hủy miễn phí trước 48 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base5star),
                    'benefits'            => json_encode(['View sông Hàn lãng mạn', 'Ban công riêng', '2 giường đơn linh hoạt']),
                    'room_notes'          => json_encode('Phòng Superior 35m² với ban công nhìn ra sông Hàn, phù hợp cho cặp đôi hoặc bạn bè.'),
                ],
            ],

            // ── Hotel 12: Naman Retreat Đà Nẵng ──────────────────────────
            [
                'hotel_id'  => 12,
                'room_name' => 'Căn Hộ 1 Phòng Ngủ Có Ban Công Hướng Nhìn Ra Biển',
                'data'      => [
                    'package_name'        => 'Căn hộ 1PN - Ban công hướng biển',
                    'room_badge'          => 'Phổ biến',
                    'cancellation_policy' => 'Hủy miễn phí trước 72 giờ nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode($base5star),
                    'benefits'            => json_encode(['View biển Non Nước', 'Ban công riêng', 'Bể bơi 100m dài']),
                    'room_notes'          => json_encode('Căn hộ 54m² với ban công hướng biển, gần bể bơi dài 100m nổi tiếng của Naman Retreat.'),
                ],
            ],
            [
                'hotel_id'  => 12,
                'room_name' => 'Biệt Thự Hồ Bơi Một Phòng Ngủ Quang Cảnh Hồ Bơi',
                'data'      => [
                    'package_name'        => 'Pool Villa 1PN - Hồ bơi riêng',
                    'room_badge'          => 'Sang trọng',
                    'cancellation_policy' => 'Hủy miễn phí trước 5 ngày nhận phòng.',
                    'is_refundable'       => 1,
                    'room_amenities'      => json_encode(array_merge($withAll5star, ['breakfast'])),
                    'benefits'            => json_encode(['Hồ bơi riêng tư', 'Biệt thự độc lập 100m²', 'Bữa sáng cho 2', 'Thiên nhiên xanh mát']),
                    'room_notes'          => json_encode('Pool Villa 100m² hoàn toàn riêng biệt với hồ bơi riêng, bao quanh bởi thiên nhiên tại khu resort sinh thái.'),
                ],
            ],
        ];

        foreach ($updates as $upd) {
            $hotelId  = $upd['hotel_id'];
            $roomName = $upd['room_name'];
            $data     = $upd['data'];
            DB::table('rooms')
                ->where('hotel_id', $hotelId)
                ->where('room_name', $roomName)
                ->whereNull('room_amenities')
                ->update($data);
        }
    }

    public function down(): void
    {
        $hotelIds = [1, 2, 3, 7, 8, 9, 10, 11, 12];
        DB::table('rooms')->whereIn('hotel_id', $hotelIds)->update([
            'package_name'        => null,
            'room_badge'          => null,
            'cancellation_policy' => null,
            'room_amenities'      => null,
            'benefits'            => null,
            'room_notes'          => null,
        ]);
    }
};
