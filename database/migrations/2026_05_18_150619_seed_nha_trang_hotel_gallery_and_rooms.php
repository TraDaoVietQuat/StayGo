<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── GALLERY: Amiana Resort Nha Trang (hotel_id = 4) ─────────────
        if (DB::table('hotel_images')->where('hotel_id', 4)->doesntExist()) {
            DB::table('hotel_images')->insert([
                ['hotel_id' => 4, 'image' => 'Amiana-Resort-Nha-Trang-3.webp',                    'caption' => 'Ho boi vo cuc nhin ra bien',    'sort_order' => 1],
                ['hotel_id' => 4, 'image' => 'amiana-resort-nha-trang-4.webp',                    'caption' => 'Bai bien rieng cua resort',      'sort_order' => 2],
                ['hotel_id' => 4, 'image' => 'nha-trang-2.webp',                                  'caption' => 'Canh bien Nha Trang',            'sort_order' => 3],
                ['hotel_id' => 4, 'image' => 'vinh-nha-trang.jpg',                                'caption' => 'Vinh Nha Trang',                 'sort_order' => 4],
                ['hotel_id' => 4, 'image' => 'hotels/gallery/01KQHE6BE09AVZR6M9XMZB1AMM.webp',   'caption' => 'Khu nghi duong',                 'sort_order' => 5],
                ['hotel_id' => 4, 'image' => 'hotels/gallery/01KQHE6BEJYDNKKKGNA5P7KXN1.webp',   'caption' => 'Villa huong bien',               'sort_order' => 6],
                ['hotel_id' => 4, 'image' => 'hotels/gallery/01KQHE6BETK2QKDS52GGC0VRQQ.webp',   'caption' => 'Khong gian nghi duong',          'sort_order' => 7],
            ]);
        }

        // ── GALLERY: Queen Ann Nha Trang (hotel_id = 5) ─────────────────
        if (DB::table('hotel_images')->where('hotel_id', 5)->doesntExist()) {
            DB::table('hotel_images')->insert([
                ['hotel_id' => 5, 'image' => 'anhbien2.jpg',                          'caption' => 'View bien toan canh',           'sort_order' => 1],
                ['hotel_id' => 5, 'image' => 'vinh-nha-trang.jpg',                    'caption' => 'Vinh Nha Trang',                'sort_order' => 2],
                ['hotel_id' => 5, 'image' => 'thap-tram-huong_1749637523.webp',       'caption' => 'Thap Tram Huong gan khach san', 'sort_order' => 3],
                ['hotel_id' => 5, 'image' => 'nha-trang-2.webp',                      'caption' => 'Bai bien Nha Trang',            'sort_order' => 4],
            ]);
        }

        // ── ROOMS ────────────────────────────────────────────────────────

        $amResort = json_encode(['breakfast','non_smoking','wifi','ac','tv','minibar','fridge','safe','desk','bathtub','shower_standing','private_bathroom','hair_dryer','bathrobe','toiletries','hot_water','free_bottled_water']);
        $amVilla  = json_encode(['breakfast','non_smoking','wifi','ac','tv','minibar','fridge','safe','desk','bathtub','shower_standing','private_bathroom','hair_dryer','bathrobe','toiletries','hot_water','free_bottled_water','waiting_area']);
        $qaDeluxe = json_encode(['breakfast','non_smoking','wifi','ac','tv','fridge','safe','desk','shower_standing','private_bathroom','hair_dryer','toiletries','hot_water','free_bottled_water','blackout_curtains']);
        $qaSuite  = json_encode(['breakfast','non_smoking','wifi','ac','tv','minibar','fridge','safe','desk','bathtub','shower_standing','private_bathroom','hair_dryer','bathrobe','toiletries','hot_water','free_bottled_water','waiting_area']);
        $citStudio= json_encode(['non_smoking','wifi','ac','tv','fridge','safe','desk','shower_standing','private_bathroom','hair_dryer','toiletries','hot_water','free_bottled_water','blackout_curtains']);
        $citApt   = json_encode(['non_smoking','wifi','ac','tv','fridge','safe','desk','bathtub','shower_standing','private_bathroom','hair_dryer','bathrobe','toiletries','hot_water','free_bottled_water','waiting_area']);

        // Room 6 — Ocean/Garden Villa (Amiana)
        DB::table('rooms')->where('id', 6)->update([
            'package_name'        => 'Bao gom bua sang & bai bien rieng',
            'bed_type'            => '1 giuong King co lon',
            'area'                => 55,
            'max_children'        => 1,
            'is_refundable'       => 1,
            'room_badge'          => 'Pho bien',
            'cancellation_policy' => 'Huy mien phi truoc 72 gio nhan phong.',
            'room_amenities'      => $amResort,
            'benefits'            => json_encode(['Bai bien rieng', 'Ho boi vo cuc', 'Bua sang cho 2']),
            'room_notes'          => json_encode('Tam nhin ra dai duong hoac vuon nhiet doi. Dien tich 55m2.'),
        ]);

        // Room 7 — Pool Villa (Amiana)
        DB::table('rooms')->where('id', 7)->update([
            'package_name'        => 'Pool Villa - Ho boi rieng + Bua sang',
            'bed_type'            => '1 giuong King co lon',
            'area'                => 120,
            'max_children'        => 1,
            'is_refundable'       => 1,
            'room_badge'          => 'Sang trong',
            'cancellation_policy' => 'Huy mien phi truoc 5 ngay nhan phong.',
            'room_amenities'      => $amVilla,
            'benefits'            => json_encode(['Ho boi rieng', 'Bai bien rieng', 'Bua sang cho 2', 'Butler rieng']),
            'room_notes'          => json_encode('Villa cao cap voi ho boi rieng biet, dien tich 120m2. Phu hop tuan trang mat.'),
        ]);

        // Room 8 — Deluxe/Executive (Queen Ann)
        DB::table('rooms')->where('id', 8)->update([
            'package_name'        => 'View bien - Bua sang cho 2',
            'bed_type'            => '1 giuong doi hoac 2 giuong don',
            'area'                => 28,
            'max_children'        => 2,
            'is_refundable'       => 1,
            'room_badge'          => 'Best Seller',
            'cancellation_policy' => 'Huy mien phi truoc 48 gio nhan phong.',
            'room_amenities'      => $qaDeluxe,
            'benefits'            => json_encode(['100% phong view bien', 'Bua sang buffet']),
            'room_notes'          => json_encode('Phong huong bien tang cao, dien tich 28m2.'),
        ]);

        // Room 9 — Suite VIP (Queen Ann)
        DB::table('rooms')->where('id', 9)->update([
            'package_name'        => 'Suite - View bien + Bua sang',
            'bed_type'            => '1 giuong King',
            'area'                => 55,
            'max_children'        => 2,
            'is_refundable'       => 1,
            'room_badge'          => 'VIP',
            'cancellation_policy' => 'Huy mien phi truoc 72 gio nhan phong.',
            'room_amenities'      => $qaSuite,
            'benefits'            => json_encode(['Phong khach rieng', '100% view bien', 'Bua sang buffet', 'Minibar']),
            'room_notes'          => json_encode('Suite 55m2 voi phong khach tach biet va view bien toan canh.'),
        ]);

        // Room 10 — Studio Heritage (Citadines)
        DB::table('rooms')->where('id', 10)->update([
            'package_name'        => 'Studio co bep nho - View thanh pho',
            'bed_type'            => '1 giuong doi',
            'area'                => 30,
            'max_children'        => 1,
            'is_refundable'       => 1,
            'room_badge'          => 'Ly tuong dai ngay',
            'cancellation_policy' => 'Huy mien phi truoc 48 gio nhan phong.',
            'room_amenities'      => $citStudio,
            'benefits'            => json_encode(['Bep nho tien nghi', 'Gan Vincom & Pho Tay', 'Giat la mien phi']),
            'room_notes'          => json_encode('Studio 30m2 voi bep nho, phu hop luu tru 3-7 ngay.'),
        ]);

        // Room 11 — Can ho 2 phong ngu (Citadines)
        DB::table('rooms')->where('id', 11)->update([
            'package_name'        => 'Can ho 2 PN - Bep day du',
            'bed_type'            => '1 giuong King + 2 giuong don',
            'area'                => 65,
            'max_guests'          => 6,
            'max_children'        => 3,
            'is_refundable'       => 1,
            'room_badge'          => 'Gia dinh',
            'cancellation_policy' => 'Huy mien phi truoc 72 gio nhan phong.',
            'room_amenities'      => $citApt,
            'benefits'            => json_encode(['2 phong ngu rieng', 'Phong khach & bep day du', 'Ly tuong cho gia dinh']),
            'room_notes'          => json_encode('Can ho 65m2 co 2 phong ngu rieng biet, phong khach va bep day du tien nghi.'),
        ]);
    }

    public function down(): void
    {
        DB::table('hotel_images')->whereIn('hotel_id', [4, 5])->delete();

        DB::table('rooms')->whereIn('id', [6, 7, 8, 9, 10, 11])->update([
            'package_name' => null, 'bed_type' => null, 'area' => null,
            'room_badge' => null, 'cancellation_policy' => null,
            'room_amenities' => null, 'benefits' => null, 'room_notes' => null,
        ]);
    }
};
