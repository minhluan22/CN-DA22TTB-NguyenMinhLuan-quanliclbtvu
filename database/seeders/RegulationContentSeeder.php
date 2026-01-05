<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegulationContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📋 Cập nhật nội dung nội quy...');

        $regulations = [
            [
                'code' => 'NQ-001',
                'title' => 'Nội quy 1',
                'content' => "**Nội quy về Tham gia Hoạt động CLB**\n\n1. **Đăng ký tham gia:**\n   - Sinh viên phải đăng ký tham gia hoạt động qua hệ thống trước thời hạn quy định.\n   - Mỗi sinh viên chỉ được đăng ký một lần cho mỗi hoạt động.\n\n2. **Điều kiện tham gia:**\n   - Phải là thành viên chính thức của CLB hoặc được mời tham gia.\n   - Đảm bảo có đủ thời gian và cam kết tham gia đầy đủ.\n\n3. **Quy trình duyệt:**\n   - Ban tổ chức sẽ xét duyệt đơn đăng ký trong vòng 24-48 giờ.\n   - Sinh viên được thông báo qua email và hệ thống.\n\n4. **Trách nhiệm khi tham gia:**\n   - Đến đúng giờ, đúng địa điểm.\n   - Tuân thủ nội quy của hoạt động.\n   - Tham gia tích cực, đóng góp ý kiến xây dựng.\n\n5. **Điểm danh và chứng nhận:**\n   - Phải điểm danh đầy đủ để được ghi nhận tham gia.\n   - Chỉ nhận điểm hoạt động khi tham gia từ 80% thời lượng trở lên.\n\n**Vi phạm:** Đăng ký nhưng không tham gia quá 3 lần sẽ bị tạm khóa quyền đăng ký trong 1 tháng.",
                'severity' => 'light'
            ],
            [
                'code' => 'NQ-002',
                'title' => 'Nội quy 2',
                'content' => "**Nội quy về Tổ chức Hoạt động CLB**\n\n1. **Đề xuất hoạt động:**\n   - Chủ nhiệm CLB hoặc Ban chủ nhiệm có quyền đề xuất hoạt động.\n   - Phải nộp đề xuất trước ít nhất 7 ngày so với ngày dự kiến tổ chức.\n\n2. **Nội dung đề xuất:**\n   - Tên hoạt động rõ ràng, có ý nghĩa.\n   - Mục tiêu, nội dung, thời gian, địa điểm cụ thể.\n   - Dự kiến số lượng người tham gia và kinh phí (nếu có).\n\n3. **Quy trình phê duyệt:**\n   - Admin xem xét và phê duyệt trong vòng 2-3 ngày làm việc.\n   - Nếu bị từ chối, sẽ có lý do cụ thể để CLB chỉnh sửa.\n\n4. **Chuẩn bị và tổ chức:**\n   - CLB chịu trách nhiệm chuẩn bị đầy đủ: địa điểm, thiết bị, tài liệu.\n   - Thông báo rộng rãi đến thành viên và sinh viên quan tâm.\n   - Đảm bảo an toàn, trật tự trong suốt hoạt động.\n\n5. **Báo cáo sau hoạt động:**\n   - Nộp báo cáo kết quả trong vòng 3 ngày sau khi kết thúc.\n   - Bao gồm: số lượng tham gia, hình ảnh, đánh giá, kinh phí thực tế.\n\n**Vi phạm:** Tổ chức hoạt động không được phê duyệt hoặc sai nội dung đã đăng ký sẽ bị xử lý nghiêm.",
                'severity' => 'medium'
            ],
            [
                'code' => 'NQ-003',
                'title' => 'Nội quy 3',
                'content' => "**Nội quy về Quản lý Thành viên CLB**\n\n1. **Điều kiện gia nhập:**\n   - Là sinh viên đang học tại trường.\n   - Có đơn đăng ký và được Ban chủ nhiệm phê duyệt.\n\n2. **Quyền lợi thành viên:**\n   - Tham gia các hoạt động của CLB.\n   - Được đào tạo kỹ năng, kiến thức chuyên môn.\n   - Nhận chứng nhận và điểm rèn luyện.\n\n3. **Nghĩa vụ thành viên:**\n   - Tham gia đầy đủ các buổi họp, hoạt động bắt buộc.\n   - Đóng góp ý kiến xây dựng CLB.\n   - Tuân thủ nội quy, quy định của CLB và nhà trường.\n\n4. **Khen thưởng:**\n   - Thành viên tích cực, có đóng góp xuất sắc sẽ được khen thưởng.\n   - Ưu tiên xét chọn vào Ban chủ nhiệm.\n\n5. **Kỷ luật:**\n   - Vi phạm nội quy sẽ bị nhắc nhở, cảnh cáo hoặc đình chỉ tư cách thành viên.\n\n**Lưu ý:** Thành viên có thể rút khỏi CLB bất kỳ lúc nào bằng đơn xin thôi.",
                'severity' => 'medium'
            ],
            [
                'code' => 'NQ-004',
                'title' => 'Nội quy 4',
                'content' => "**Nội quy về Tài chính và Tài sản CLB**\n\n1. **Quản lý tài chính:**\n   - Mọi khoản thu chi phải được ghi chép đầy đủ, minh bạch.\n   - Trưởng ban Tài chính chịu trách nhiệm quản lý và báo cáo định kỳ.\n\n2. **Nguồn thu:**\n   - Hỗ trợ từ nhà trường (nếu có).\n   - Tài trợ từ doanh nghiệp, cá nhân.\n   - Đóng góp tự nguyện của thành viên.\n\n3. **Chi tiêu:**\n   - Phải có kế hoạch chi tiết, được Ban chủ nhiệm phê duyệt.\n   - Ưu tiên chi cho hoạt động chính, có ý nghĩa.\n   - Giữ hóa đơn, chứng từ đầy đủ.\n\n4. **Quản lý tài sản:**\n   - Tài sản của CLB phải được đăng ký, kiểm kê định kỳ.\n   - Thành viên mượn tài sản phải có phiếu mượn, cam kết bồi thường nếu hư hỏng.\n\n5. **Báo cáo:**\n   - Báo cáo tài chính hàng tháng cho Ban chủ nhiệm.\n   - Báo cáo tổng kết cuối học kỳ, năm học.\n\n**Vi phạm:** Sử dụng tài chính, tài sản sai mục đích sẽ bị xử lý kỷ luật nghiêm khắc.",
                'severity' => 'serious'
            ],
            [
                'code' => 'NQ-005',
                'title' => 'Nội quy 5',
                'content' => "**Nội quy về Truyền thông và Hình ảnh CLB**\n\n1. **Quản lý kênh truyền thông:**\n   - Mọi kênh chính thức (Facebook, Website, Zalo) phải được Ban Truyền thông quản lý.\n   - Mật khẩu được lưu trữ an toàn, chỉ Ban chủ nhiệm biết.\n\n2. **Nội dung đăng tải:**\n   - Phải phù hợp với định hướng của CLB và nhà trường.\n   - Không đăng nội dung vi phạm pháp luật, đạo đức.\n   - Hình ảnh, video phải rõ nét, chuyên nghiệp.\n\n3. **Quy trình đăng bài:**\n   - Nội dung quan trọng phải được Chủ nhiệm duyệt trước khi đăng.\n   - Thông tin hoạt động phải đăng sớm để sinh viên biết và đăng ký.\n\n4. **Tương tác với cộng đồng:**\n   - Trả lời tin nhắn, bình luận nhanh chóng, lịch sự.\n   - Xử lý phản hồi tiêu cực một cách chuyên nghiệp.\n\n5. **Bảo vệ hình ảnh CLB:**\n   - Không sử dụng tên, logo CLB cho mục đích cá nhân.\n   - Báo cáo ngay nếu phát hiện tài khoản giả mạo.\n\n**Lưu ý:** Hình ảnh CLB là tài sản chung, mọi thành viên đều có trách nhiệm bảo vệ.",
                'severity' => 'medium'
            ],
            [
                'code' => 'NQ-006',
                'title' => 'Nội quy 6',
                'content' => "**Nội quy về An toàn và Trật tự**\n\n1. **An toàn trong hoạt động:**\n   - Mọi hoạt động phải đảm bảo an toàn tuyệt đối cho người tham gia.\n   - Có phương án dự phòng cho các tình huống khẩn cấp.\n\n2. **Sử dụng thiết bị:**\n   - Chỉ sử dụng thiết bị khi đã được hướng dẫn đầy đủ.\n   - Báo cáo ngay nếu thiết bị hư hỏng hoặc có dấu hiệu bất thường.\n\n3. **Giữ gìn trật tự:**\n   - Không gây ồn ào, ảnh hưởng đến hoạt động khác.\n   - Giữ vệ sinh chung, dọn dẹp sau khi kết thúc.\n\n4. **Xử lý sự cố:**\n   - Thông báo ngay cho Ban tổ chức khi có sự cố.\n   - Hợp tác với nhà trường, cơ quan chức năng khi cần thiết.\n\n5. **Bảo hiểm:**\n   - Khuyến khích thành viên tham gia bảo hiểm tai nạn.\n   - CLB mua bảo hiểm cho các hoạt động có rủi ro cao.\n\n**Vi phạm:** Gây mất an toàn, trật tự nghiêm trọng sẽ bị đình chỉ hoạt động và xử lý kỷ luật.",
                'severity' => 'serious'
            ],
            [
                'code' => 'NQ-007',
                'title' => 'Nội quy 7',
                'content' => "**Nội quy về Đạo đức và Văn hóa ứng xử**\n\n1. **Tôn trọng:**\n   - Tôn trọng lẫn nhau, không phân biệt đối xử.\n   - Lắng nghe ý kiến, không áp đặt quan điểm cá nhân.\n\n2. **Trung thực:**\n   - Trung thực trong mọi hoạt động, không gian lận.\n   - Thừa nhận sai lầm và sẵn sàng sửa chữa.\n\n3. **Trách nhiệm:**\n   - Hoàn thành nhiệm vụ được giao đúng hạn.\n   - Chủ động hỗ trợ đồng đội khi cần thiết.\n\n4. **Đoàn kết:**\n   - Xây dựng tinh thần đoàn kết, gắn bó trong CLB.\n   - Không gây mâu thuẫn, chia rẽ nội bộ.\n\n5. **Tích cực:**\n   - Luôn có thái độ tích cực, lạc quan.\n   - Đóng góp ý tưởng sáng tạo cho CLB.\n\n**Lưu ý:** Đạo đức và văn hóa ứng xử là nền tảng để CLB phát triển bền vững.",
                'severity' => 'medium'
            ],
            [
                'code' => 'NQ-008',
                'title' => 'Nội quy 8',
                'content' => "**Nội quy về Họp và Ra quyết định**\n\n1. **Cuộc họp định kỳ:**\n   - Ban chủ nhiệm họp ít nhất 2 lần/tháng.\n   - Toàn thể thành viên họp ít nhất 1 lần/tháng.\n\n2. **Thông báo họp:**\n   - Thông báo trước ít nhất 3 ngày.\n   - Gửi nội dung, chương trình họp rõ ràng.\n\n3. **Tham dự:**\n   - Thành viên phải tham dự đầy đủ.\n   - Nếu vắng mặt phải xin phép trước và có lý do chính đáng.\n\n4. **Quy trình họp:**\n   - Có chủ tọa, thư ký ghi biên bản.\n   - Mọi người được quyền phát biểu, đóng góp ý kiến.\n\n5. **Ra quyết định:**\n   - Quyết định quan trọng phải qua biểu quyết.\n   - Tuân theo nguyên tắc đa số (trên 50% tán thành).\n\n**Lưu ý:** Biên bản họp phải được lưu trữ đầy đủ để tra cứu khi cần.",
                'severity' => 'light'
            ],
            [
                'code' => 'NQ-009',
                'title' => 'Nội quy 9',
                'content' => "**Nội quy về Hợp tác và Liên kết**\n\n1. **Hợp tác với CLB khác:**\n   - Khuyến khích hợp tác, tổ chức hoạt động chung.\n   - Phải có thỏa thuận rõ ràng về trách nhiệm, quyền lợi.\n\n2. **Liên kết với doanh nghiệp:**\n   - Tìm kiếm tài trợ, cơ hội thực tập cho thành viên.\n   - Đảm bảo uy tín, không làm ảnh hưởng đến hình ảnh CLB.\n\n3. **Tham gia sự kiện bên ngoài:**\n   - Đại diện CLB phải được Ban chủ nhiệm chỉ định.\n   - Tuân thủ nội quy của đơn vị tổ chức.\n\n4. **Chia sẻ kinh nghiệm:**\n   - Tham gia các diễn đàn, hội thảo về hoạt động CLB.\n   - Học hỏi mô hình hay từ các CLB khác.\n\n5. **Bảo vệ thông tin:**\n   - Không tiết lộ thông tin nội bộ cho bên ngoài.\n   - Ký cam kết bảo mật khi cần thiết.\n\n**Lưu ý:** Hợp tác và liên kết giúp CLB phát triển, mở rộng mạng lưới.",
                'severity' => 'light'
            ],
            [
                'code' => 'NQ-010',
                'title' => 'Nội quy 10',
                'content' => "**Nội quy về Đào tạo và Phát triển**\n\n1. **Đào tạo thành viên mới:**\n   - Tổ chức training cho thành viên mới về CLB, nội quy, kỹ năng cơ bản.\n   - Giao mentor hỗ trợ thành viên mới hòa nhập.\n\n2. **Phát triển kỹ năng:**\n   - Tổ chức workshop, seminar về kỹ năng mềm, chuyên môn.\n   - Khuyến khích thành viên tự học, tự phát triển.\n\n3. **Đánh giá năng lực:**\n   - Đánh giá định kỳ để phát hiện điểm mạnh, điểm yếu.\n   - Có kế hoạch đào tạo, bồi dưỡng phù hợp.\n\n4. **Thăng tiến:**\n   - Thành viên xuất sắc được xét chọn vào Ban chủ nhiệm.\n   - Quy trình minh bạch, công bằng.\n\n5. **Lưu trữ hồ sơ:**\n   - Lưu trữ hồ sơ thành viên, quá trình hoạt động.\n   - Cấp chứng nhận khi thành viên hoàn thành nhiệm kỳ.\n\n**Lưu ý:** Đào tạo và phát triển là chìa khóa để CLB có nguồn nhân lực chất lượng.",
                'severity' => 'light'
            ],
        ];

        $updated = 0;
        foreach ($regulations as $data) {
            $regulation = DB::table('regulations')
                ->where('code', $data['code'])
                ->first();

            if ($regulation) {
                DB::table('regulations')
                    ->where('code', $data['code'])
                    ->update([
                        'title' => $data['title'],
                        'content' => $data['content'],
                        'severity' => $data['severity'],
                        'updated_at' => Carbon::now()
                    ]);
                $updated++;
                $this->command->info("  ✅ Cập nhật nội dung cho {$data['code']}");
            }
        }

        $this->command->info("✅ Đã cập nhật nội dung cho {$updated} nội quy.");
    }
}

