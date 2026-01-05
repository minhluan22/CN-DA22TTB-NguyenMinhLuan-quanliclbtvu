<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy admin đầu tiên làm người gửi (role_id = 1)
        $admin = User::where('role_id', 1)->first();

        if (!$admin) {
            $this->command->warn('Không tìm thấy Admin. Vui lòng tạo Admin trước khi chạy seeder này.');
            return;
        }

        $notifications = [
            // 1. Thông báo chung – toàn hệ thống
            [
                'title' => 'Thông báo cập nhật hệ thống quản lý câu lạc bộ',
                'body' => "Ban Quản trị thông báo hệ thống quản lý câu lạc bộ đã được cập nhật các chức năng mới nhằm nâng cao trải nghiệm người dùng. Sinh viên vui lòng đăng nhập và kiểm tra thông tin cá nhân để đảm bảo dữ liệu chính xác.",
                'type' => 'system',
                'target_type' => 'all',
            ],
            // 2. Thông báo xác thực tài khoản
            [
                'title' => 'Yêu cầu hoàn tất xác thực tài khoản',
                'body' => "Sinh viên vui lòng hoàn tất xác thực email để đảm bảo đầy đủ quyền truy cập vào các chức năng của hệ thống. Các tài khoản chưa xác thực có thể bị hạn chế một số quyền sử dụng.",
                'type' => 'system',
                'target_type' => 'students',
            ],
            // 3. Thông báo đăng ký CLB
            [
                'title' => 'Mở đăng ký tham gia câu lạc bộ học kỳ mới',
                'body' => "Nhà trường chính thức mở cổng đăng ký tham gia các câu lạc bộ sinh viên trong học kỳ mới. Sinh viên truy cập hệ thống, chọn câu lạc bộ phù hợp và gửi yêu cầu tham gia trước thời hạn quy định.",
                'type' => 'administrative',
                'target_type' => 'students',
            ],
            // 4. Thông báo thời hạn đăng ký
            [
                'title' => 'Nhắc nhở hạn cuối đăng ký CLB',
                'body' => "Thời hạn đăng ký tham gia câu lạc bộ sẽ kết thúc trong thời gian sắp tới. Sinh viên chưa đăng ký vui lòng hoàn tất thủ tục trước khi hệ thống đóng cổng đăng ký.",
                'type' => 'administrative',
                'target_type' => 'students',
            ],
            // 5. Thông báo quy định chung
            [
                'title' => 'Ban hành nội quy chung dành cho sinh viên tham gia CLB',
                'body' => "Ban Quản trị ban hành nội quy chung áp dụng cho tất cả sinh viên tham gia câu lạc bộ. Sinh viên có trách nhiệm đọc, hiểu và tuân thủ đầy đủ các quy định đã được công bố trên hệ thống.",
                'type' => 'regulation',
                'target_type' => 'all',
            ],
            // 6. Thông báo cập nhật nội quy
            [
                'title' => 'Cập nhật nội quy câu lạc bộ',
                'body' => "Một số nội dung trong nội quy câu lạc bộ đã được điều chỉnh nhằm phù hợp với tình hình thực tế. Sinh viên vui lòng truy cập mục Nội quy – Vi phạm để xem chi tiết.",
                'type' => 'regulation',
                'target_type' => 'all',
            ],
            // 7. Thông báo điểm hoạt động
            [
                'title' => 'Cập nhật điểm hoạt động sinh viên',
                'body' => "Hệ thống đã cập nhật điểm hoạt động cá nhân dựa trên các hoạt động sinh viên đã tham gia. Sinh viên kiểm tra và phản hồi nếu phát hiện sai sót.",
                'type' => 'system',
                'target_type' => 'students',
            ],
            // 8. Thông báo học kỳ mới
            [
                'title' => 'Thông báo bắt đầu học kỳ mới',
                'body' => "Nhà trường chính thức bắt đầu học kỳ mới. Các câu lạc bộ sẽ triển khai kế hoạch hoạt động theo lịch đã đăng ký. Sinh viên theo dõi thông báo để không bỏ lỡ thông tin quan trọng.",
                'type' => 'administrative',
                'target_type' => 'all',
            ],
            // 9. Thông báo hoạt động toàn trường
            [
                'title' => 'Tham gia hoạt động phong trào cấp trường',
                'body' => "Nhà trường tổ chức hoạt động phong trào cấp trường dành cho sinh viên. Các câu lạc bộ và thành viên quan tâm vui lòng theo dõi thông tin chi tiết tại mục Hoạt động.",
                'type' => 'administrative',
                'target_type' => 'all',
            ],
            // 10. Thông báo bảo trì hệ thống
            [
                'title' => 'Thông báo bảo trì hệ thống',
                'body' => "Hệ thống sẽ tạm ngưng hoạt động trong thời gian bảo trì theo kế hoạch. Sinh viên chủ động sắp xếp thời gian sử dụng và theo dõi thông báo khi hệ thống hoạt động trở lại.",
                'type' => 'system',
                'target_type' => 'all',
            ],
            // 11. Thông báo cập nhật thông tin cá nhân
            [
                'title' => 'Yêu cầu cập nhật thông tin cá nhân',
                'body' => "Sinh viên vui lòng kiểm tra và cập nhật đầy đủ thông tin cá nhân trên hệ thống để đảm bảo dữ liệu chính xác phục vụ cho công tác quản lý.",
                'type' => 'administrative',
                'target_type' => 'students',
            ],
            // 12. Thông báo sử dụng hệ thống đúng mục đích
            [
                'title' => 'Nhắc nhở sử dụng hệ thống đúng quy định',
                'body' => "Hệ thống quản lý câu lạc bộ chỉ được sử dụng cho các mục đích học tập và hoạt động phong trào. Mọi hành vi sử dụng sai mục đích sẽ bị xử lý theo quy định.",
                'type' => 'regulation',
                'target_type' => 'all',
            ],
            // 13. Thông báo quyền và nghĩa vụ sinh viên
            [
                'title' => 'Quyền và nghĩa vụ của sinh viên khi tham gia CLB',
                'body' => "Sinh viên khi tham gia câu lạc bộ có quyền và nghĩa vụ theo quy định của nhà trường. Vui lòng tham khảo chi tiết tại mục Nội quy – Vi phạm.",
                'type' => 'regulation',
                'target_type' => 'students',
            ],
            // 14. Thông báo kết quả xử lý vi phạm (chung)
            [
                'title' => 'Thông báo về việc xử lý vi phạm nội quy',
                'body' => "Ban Quản trị đã tiến hành xử lý một số trường hợp vi phạm nội quy câu lạc bộ. Sinh viên cần nghiêm túc chấp hành để tránh các hình thức kỷ luật không mong muốn.",
                'type' => 'regulation',
                'target_type' => 'all',
            ],
            // 15. Thông báo khảo sát ý kiến
            [
                'title' => 'Khảo sát ý kiến sinh viên về hoạt động CLB',
                'body' => "Nhà trường tổ chức khảo sát nhằm thu thập ý kiến sinh viên về chất lượng hoạt động câu lạc bộ. Sinh viên vui lòng tham gia khảo sát để góp phần cải thiện hệ thống.",
                'type' => 'administrative',
                'target_type' => 'students',
            ],
            // 16. Thông báo lịch tổng kết
            [
                'title' => 'Thông báo tổng kết hoạt động CLB',
                'body' => "Cuối học kỳ, nhà trường sẽ tiến hành tổng kết hoạt động các câu lạc bộ. Sinh viên theo dõi thông báo từ CLB để nắm rõ thời gian và nội dung tổng kết.",
                'type' => 'administrative',
                'target_type' => 'all',
            ],
            // 17. Thông báo thay đổi nhân sự CLB
            [
                'title' => 'Thông báo thay đổi nhân sự quản lý CLB',
                'body' => "Một số câu lạc bộ có sự thay đổi về nhân sự quản lý. Sinh viên vui lòng cập nhật thông tin mới để thuận tiện trong quá trình sinh hoạt CLB.",
                'type' => 'administrative',
                'target_type' => 'all',
            ],
            // 18. Thông báo hướng dẫn sử dụng hệ thống
            [
                'title' => 'Hướng dẫn sử dụng hệ thống quản lý CLB',
                'body' => "Sinh viên mới tham gia hệ thống vui lòng tham khảo tài liệu hướng dẫn sử dụng để nắm rõ các chức năng và thao tác cơ bản.",
                'type' => 'system',
                'target_type' => 'students',
            ],
            // 19. Thông báo kết thúc năm học
            [
                'title' => 'Thông báo kết thúc năm học',
                'body' => "Năm học đã kết thúc. Nhà trường ghi nhận sự tham gia tích cực của sinh viên trong các hoạt động câu lạc bộ và mong tiếp tục nhận được sự đồng hành trong năm học tới.",
                'type' => 'administrative',
                'target_type' => 'all',
            ],
            // 20. Thông báo chung cuối kỳ
            [
                'title' => 'Thông báo chung cuối học kỳ',
                'body' => "Ban Quản trị cảm ơn sinh viên đã tích cực tham gia các hoạt động câu lạc bộ trong suốt học kỳ vừa qua. Chúc sinh viên có kỳ nghỉ an toàn và hiệu quả.",
                'type' => 'administrative',
                'target_type' => 'all',
            ],
        ];

        $this->command->info('Đang tạo 20 thông báo mẫu...');

        foreach ($notifications as $index => $notificationData) {
            // Tạo thông báo với thời gian gửi cách nhau 1 ngày (từ 20 ngày trước đến hiện tại)
            $sentAt = Carbon::now()->subDays(20 - $index);

            $notification = Notification::create([
                'title' => $notificationData['title'],
                'body' => $notificationData['body'],
                'sender_id' => $admin->id,
                'type' => $notificationData['type'],
                'target_type' => $notificationData['target_type'],
                'target_ids' => null,
                'sent_at' => $sentAt,
                'status' => 'sent',
                'is_public' => true,
                'notification_source' => 'admin',
                'club_id' => null,
            ]);

            // Xác định danh sách người nhận
            $recipients = [];
            
            if ($notificationData['target_type'] === 'all') {
                // Tất cả người dùng (trừ admin)
                $users = User::where('role_id', '!=', 1)->get();
                foreach ($users as $user) {
                    $recipients[] = [
                        'notification_id' => $notification->id,
                        'user_id' => $user->id,
                        'club_id' => null,
                        'is_read' => false,
                        'created_at' => $sentAt,
                        'updated_at' => $sentAt,
                    ];
                }
            } elseif ($notificationData['target_type'] === 'students') {
                // Tất cả sinh viên
                $students = User::where('role_id', 2)->get();
                foreach ($students as $student) {
                    $recipients[] = [
                        'notification_id' => $notification->id,
                        'user_id' => $student->id,
                        'club_id' => null,
                        'is_read' => false,
                        'created_at' => $sentAt,
                        'updated_at' => $sentAt,
                    ];
                }
            }

            // Chèn batch để tối ưu hiệu suất
            if (!empty($recipients)) {
                // Chia nhỏ thành các batch 500 để tránh lỗi memory
                $chunks = array_chunk($recipients, 500);
                foreach ($chunks as $chunk) {
                    NotificationRecipient::insert($chunk);
                }
            }

            $this->command->info("✓ Đã tạo thông báo: {$notificationData['title']} ({$notificationData['target_type']}) - " . count($recipients) . " người nhận");
        }

        $this->command->info('✅ Hoàn tất! Đã tạo 20 thông báo mẫu.');
    }
}
