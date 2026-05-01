<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReplaceBlogPostsSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: skip nếu đã có dữ liệu
        if (DB::table('blog_posts')->count() > 0) {
            $this->command->info('Blog posts already seeded, skipping.');
            return;
        }

        DB::table('blog_posts')->truncate();

        $posts = [
            [
                'title'      => 'ĐÀ LẠT – THÀNH PHỐ NGÀN HOA GIỮA CAO NGUYÊN',
                'category'   => 'Đà Lạt',
                'summary'    => 'Đà Lạt — thành phố của những buổi sáng sương mù huyền ảo, đồi hoa dã quỳ rực vàng và những quán cà phê ấm áp giữa lòng rừng thông. Mỗi góc phố là một bức tranh, mỗi hơi thở là sự bình yên.',
                'content'    => '<p>Đà Lạt là thành phố duy nhất ở Việt Nam mang trong mình cả bốn mùa trong một ngày — buổi sáng mát lành, trưa ấm áp, chiều se lạnh và đêm xuống với những làn sương trắng xóa phủ khắp thung lũng.</p><p>Nằm trên cao nguyên Lâm Viên ở độ cao 1.500m so với mực nước biển, Đà Lạt sở hữu một khí hậu đặc biệt không nơi nào có được. Nhiệt độ trung bình từ 15–25°C quanh năm, không có mùa hè oi bức, không có mùa đông giá lạnh — chỉ có sự mát mẻ dịu dàng kéo dài suốt 365 ngày.</p><p>Đến Đà Lạt, bạn không thể bỏ qua Hồ Xuân Hương thơ mộng, vườn hoa thành phố rực rỡ sắc màu, Thác Datanla hùng vĩ hay những con đường đèo uốn lượn giữa rừng thông xanh. Đặc biệt, Đà Lạt còn là thiên đường ẩm thực với bánh tráng nướng, bắp xào bơ, sữa đậu nành nóng và vô vàn loại mứt hoa quả đặc trưng.</p><p>StayGo kết nối bạn với những khách sạn và resort tốt nhất tại Đà Lạt — từ villa Pháp cổ điển giữa đồi thông đến homestay ấm cúng ngay trung tâm thành phố.</p>',
                'thumb'      => 'blog/blog_1774108299_6903.jpg',
                'img'        => 'blog/blog_1774108304_1994.jpg',
                'author'     => 'Đinh Văn Thắng',
                'tags'       => json_encode(['Đà Lạt', 'du lịch', 'cao nguyên', 'nghỉ dưỡng']),
                'read_time'  => 5,
                'is_active'  => true,
                'created_at' => Carbon::create(2026, 3, 8),
                'updated_at' => Carbon::create(2026, 3, 8),
            ],
            [
                'title'      => 'NHA TRANG – THIÊN ĐƯỜNG BIỂN XANH MIỀN TRUNG',
                'category'   => 'Nha Trang',
                'summary'    => 'Bãi biển dài 6km cát trắng mịn, làn nước trong xanh như ngọc bích và hàng dừa xanh rì ven biển — Nha Trang xứng danh "Vịnh đẹp nhất thế giới" được UNESCO công nhận.',
                'content'    => '<p>Nha Trang là thành phố biển nổi tiếng nhất Việt Nam, thuộc tỉnh Khánh Hòa, nằm bên vịnh biển đẹp được xếp vào danh sách những vịnh đẹp nhất thế giới. Với bờ biển dài hơn 6km, bãi cát trắng mịn và làn nước trong xanh tuyệt vời, Nha Trang luôn là điểm đến mơ ước của hàng triệu du khách trong và ngoài nước.</p><p>Điểm thu hút đặc biệt của Nha Trang là hệ thống đảo phong phú — từ Hòn Mun (khu bảo tồn biển với san hô đầy màu sắc), Hòn Tằm (resort sang trọng giữa biển), đến Hòn Chồng và Hòn Đỏ với những khối đá granite kỳ vĩ. Lặn ngắm san hô tại Nha Trang là trải nghiệm không thể bỏ lỡ.</p><p>Ẩm thực Nha Trang cũng là một hành trình khám phá riêng — bún cá, nem nướng, bánh canh chả cá, hay những con tôm hùm tươi rói vừa đánh bắt từ biển lên. Phố Tây sầm uất với hàng quán đến 2–3 giờ sáng, beer club và nhạc sống rộn ràng.</p><p>Cùng StayGo tìm kiếm resort và khách sạn tốt nhất Nha Trang — từ view biển tuyệt đẹp đến bãi biển riêng sang trọng.</p>',
                'thumb'      => 'blog/blog_1774108596_4368.jpg',
                'img'        => 'blog/blog_1774108590_1379.jpg',
                'author'     => 'Lê Thị Hương',
                'tags'       => json_encode(['Nha Trang', 'biển', 'resort', 'lặn biển']),
                'read_time'  => 5,
                'is_active'  => true,
                'created_at' => Carbon::create(2026, 3, 2),
                'updated_at' => Carbon::create(2026, 3, 2),
            ],
            [
                'title'      => 'VŨNG TÀU – ĐIỂM NGHỈ DƯỠNG CUỐI TUẦN LÝ TƯỞNG',
                'category'   => 'Vũng Tàu',
                'summary'    => 'Chỉ cách TP.HCM khoảng 125km, Vũng Tàu là điểm chạy trốn cuối tuần hoàn hảo với bãi biển đẹp, hải sản tươi ngon và không khí biển trong lành, thư thái.',
                'content'    => '<p>Vũng Tàu — thành phố biển nằm trên bán đảo Vũng Tàu thuộc tỉnh Bà Rịa - Vũng Tàu, cách TP.HCM chỉ 125km. Với khoảng cách gần và giao thông thuận tiện (cả đường bộ lẫn phà cao tốc), Vũng Tàu trở thành điểm đến cuối tuần số một của người dân miền Nam.</p><p>Vũng Tàu có hai bãi biển chính: Bãi Trước (Bãi Thùy Dương) yên tĩnh, phù hợp cho gia đình với làn nước trong và sóng nhỏ; và Bãi Sau (Bãi Thùy Vân) dài hơn 8km, sóng lớn hơn, thích hợp cho các hoạt động lướt sóng và thể thao biển. Tượng Chúa Kitô Vua trên đỉnh núi Nhỏ là biểu tượng nổi tiếng nhất của thành phố, có thể nhìn thấy từ xa.</p><p>Hải sản Vũng Tàu nổi tiếng với giá bình dân nhưng cực kỳ tươi ngon — tôm, cua, mực, bạch tuộc, cá... được chế biến đa dạng từ hấp, nướng đến lẩu chua cay. Chợ Vũng Tàu và khu chợ đêm hải sản là điểm dừng chân không thể bỏ qua.</p>',
                'thumb'      => 'blog/blog_1774108823_6978.jpg',
                'img'        => 'blog/blog_1774108828_2147.jpg',
                'author'     => 'Võ Thị Lan',
                'tags'       => json_encode(['Vũng Tàu', 'biển', 'hải sản', 'cuối tuần']),
                'read_time'  => 4,
                'is_active'  => true,
                'created_at' => Carbon::create(2026, 2, 20),
                'updated_at' => Carbon::create(2026, 2, 20),
            ],
            [
                'title'      => 'ĐÀ NẴNG – THÀNH PHỐ ĐÁNG SỐNG NHẤT VIỆT NAM',
                'category'   => 'Đà Nẵng',
                'summary'    => 'Cầu Rồng phun lửa, bãi biển Mỹ Khê trong xanh, Bà Nà Hills huyền ảo và cách Hội An chỉ 30 phút — Đà Nẵng là điểm đến trọn gói hoàn hảo cho mọi du khách.',
                'content'    => '<p>Đà Nẵng liên tục được bình chọn là thành phố đáng sống nhất Việt Nam và một trong những điểm đến hàng đầu châu Á. Nằm ở trung điểm của dải đất hình chữ S, Đà Nẵng sở hữu vị trí địa lý độc đáo — phía Đông là biển xanh bất tận, phía Tây là núi rừng Sơn Trà xanh ngát, phía Nam gần kề phố cổ Hội An và phía Bắc có Huế cố đô nghìn năm lịch sử.</p><p>Biển Mỹ Khê — bãi biển dài 900m được tạp chí Forbes bình chọn là một trong 6 bãi biển quyến rũ nhất hành tinh — là điểm đến không thể thiếu khi đến Đà Nẵng. Nước biển trong vắt, sóng đều, bờ cát mịn trắng trải dài, cùng hệ thống cơ sở hạ tầng du lịch hiện đại.</p><p>Về đêm, Đà Nẵng trở nên lung linh với Cầu Rồng phun lửa và phun nước vào cuối tuần, Cầu Sông Hàn quay, khu phố đi bộ An Thượng tấp nập và hàng trăm nhà hàng, quán bar, club sầm uất ven biển.</p>',
                'thumb'      => 'blog/blog_1774104840_5615.jpg',
                'img'        => 'blog/blog_1774104847_2975.jpg',
                'author'     => 'Nguyễn Minh Tuấn',
                'tags'       => json_encode(['Đà Nẵng', 'biển Mỹ Khê', 'Cầu Rồng', 'Hội An']),
                'read_time'  => 6,
                'is_active'  => true,
                'created_at' => Carbon::create(2026, 2, 15),
                'updated_at' => Carbon::create(2026, 2, 15),
            ],
            [
                'title'      => 'BÀ NÀ HILLS – THIÊN ĐƯỜNG TRÊN MÂY ĐÀ NẴNG',
                'category'   => 'Đà Nẵng',
                'summary'    => 'Ở độ cao 1.487m so với mực nước biển, Bà Nà Hills là khu nghỉ mát nổi tiếng với Cầu Vàng huyền thoại, làng Pháp cổ kính và khí hậu trong lành mát mẻ quanh năm.',
                'content'    => '<p>Bà Nà Hills — khu du lịch trên đỉnh núi Chúa thuộc huyện Hòa Vang, Đà Nẵng — là một trong những điểm đến nổi tiếng nhất Việt Nam và khu vực Đông Nam Á. Hệ thống cáp treo Bà Nà từng giữ 4 kỷ lục thế giới, đưa du khách từ chân núi lên đỉnh trong chưa đầy 20 phút.</p><p>Điểm nhấn nổi bật nhất của Bà Nà Hills chính là Cầu Vàng — cây cầu được đỡ bởi hai bàn tay đá khổng lồ, trở thành hiện tượng viral toàn cầu năm 2018 và xuất hiện trên hàng triệu tấm ảnh Instagram. Đứng trên Cầu Vàng giữa biển mây trắng bồng bềnh là trải nghiệm không thể quên.</p><p>Ngoài Cầu Vàng, Bà Nà còn có Làng Pháp với kiến trúc châu Âu cổ điển, vườn hoa Le Jardin đẹp như cổ tích, công viên Fantasy Park với hàng chục trò chơi cảm giác mạnh và khu ẩm thực đa dạng phong phú.</p>',
                'thumb'      => 'blog/blog_1774108966_8518.jpg',
                'img'        => 'blog/blog_1774108970_3500.jpg',
                'author'     => 'Trần Thị Mai',
                'tags'       => json_encode(['Đà Nẵng', 'Bà Nà Hills', 'Cầu Vàng', 'du lịch']),
                'read_time'  => 5,
                'is_active'  => true,
                'created_at' => Carbon::create(2026, 2, 8),
                'updated_at' => Carbon::create(2026, 2, 8),
            ],
            [
                'title'      => 'AMIANA RESORT – THIÊN ĐƯỜNG NGHỈ DƯỠNG NHA TRANG',
                'category'   => 'Nha Trang',
                'summary'    => 'Ẩn mình bên bến du thuyền Ana Marina, Amiana Resort Nha Trang là khu nghỉ dưỡng 5 sao với bãi biển riêng, hồ bơi vô cực tuyệt đẹp và các villa riêng biệt đẳng cấp quốc tế.',
                'content'    => '<p>Nếu bạn đang tìm kiếm một kỳ nghỉ dưỡng thực sự xa hoa giữa lòng Nha Trang, Amiana Resort chính là câu trả lời hoàn hảo. Resort 5 sao này ẩn mình trên một bán đảo nhỏ xinh bên cạnh bến du thuyền Ana Marina, cách trung tâm Nha Trang chỉ 15 phút, nhưng lại mang đến cảm giác hoàn toàn tách biệt với thế giới ồn ào bên ngoài.</p><p>Amiana sở hữu bãi biển riêng trải dài với cát trắng tinh, làn nước trong xanh không một bụi bẩn và hồ bơi vô cực nhìn ra mặt biển rộng lớn. Các Ocean Villa và Garden Villa đều được thiết kế theo phong cách nhiệt đới sang trọng, với nội thất cao cấp, bồn tắm ngoài trời và ban công riêng nhìn ra biển.</p><p>Dịch vụ spa tại Amiana được đánh giá cao nhất Nha Trang — các liệu pháp massage truyền thống Việt Nam kết hợp với công nghệ chăm sóc hiện đại. Nhà hàng Ana Bay phục vụ ẩm thực Địa Trung Hải và hải sản Nha Trang trong không gian lộng gió hướng biển.</p>',
                'thumb'      => 'blog/blog_1773510795_3672.jpg',
                'img'        => 'blog/blog_1773577739_8834.jpg',
                'author'     => 'Phạm Quốc Hùng',
                'tags'       => json_encode(['Nha Trang', 'resort', '5 sao', 'Amiana']),
                'read_time'  => 4,
                'is_active'  => true,
                'created_at' => Carbon::create(2026, 1, 25),
                'updated_at' => Carbon::create(2026, 1, 25),
            ],
        ];

        DB::table('blog_posts')->insert($posts);

        // Xóa cache
        DB::table('cache')->where('key', 'like', 'home.%')
                          ->orWhere('key', 'like', 'blog.%')
                          ->delete();

        $this->command->info('✅ Đã thay thế ' . count($posts) . ' bài viết blog mới về Đà Lạt, Nha Trang, Vũng Tàu, Đà Nẵng.');
    }
}
