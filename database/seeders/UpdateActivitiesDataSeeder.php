<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\Club;
use App\Models\User;
use Carbon\Carbon;

class UpdateActivitiesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu cập nhật dữ liệu hoạt động...');

        // 1. Cập nhật tên hoạt động
        $this->updateActivityTitles();
        
        // 2. Cập nhật creator (90 do chủ nhiệm/phó chủ nhiệm, 30 do sinh viên)
        $this->updateActivityCreators();
        
        // 3. Thêm dữ liệu đăng ký/tham gia
        $this->addEventRegistrations();
        
        // 4. Cập nhật status theo thời gian thực tế
        $this->updateActivityStatus();

        $this->command->info('✅ Hoàn thành cập nhật dữ liệu hoạt động!');
    }

    /**
     * Cập nhật tên hoạt động
     */
    private function updateActivityTitles()
    {
        $this->command->info('📝 Cập nhật tên hoạt động...');

        $events = Event::all();
        $updated = 0;

        // Map tên CLB với danh sách hoạt động phù hợp
        $clubActivities = [
            'IT' => [
                'Workshop Lập trình Web với Laravel',
                'Cuộc thi Lập trình ACM/ICPC',
                'Seminar về Trí tuệ nhân tạo',
                'Hackathon 24h Coding Challenge',
                'Workshop Phát triển Mobile App',
                'Training về An ninh mạng',
                'Competition Lập trình Python',
                'Workshop DevOps và CI/CD',
                'Seminar Blockchain và Cryptocurrency',
                'Hội thảo Machine Learning cơ bản',
            ],
            'ÂM NHẠC' => [
                'Biểu diễn Acoustic Night',
                'Chương trình Biểu diễn Gây Quỹ Âm Nhạc',
                'Workshop Thanh nhạc cơ bản',
                'Concert Sinh viên Tài năng',
                'Lớp học Guitar cho người mới bắt đầu',
                'Biểu diễn Đờn ca tài tử',
                'Chương trình Ca nhạc Giao lưu',
                'Workshop Sáng tác Nhạc',
                'Showcase Band Sinh viên',
                'Concert Unplugged',
            ],
            'VIỆC LÀM' => [
                'Workshop Kinh doanh Online',
                'Ngày hội Việc làm Sinh viên',
                'Hội thảo Kỹ năng Phỏng vấn',
                'Seminar Khởi nghiệp',
                'Workshop Xây dựng CV chuyên nghiệp',
                'Tọa đàm Doanh nhân trẻ',
                'Career Fair 2026',
                'Workshop Kỹ năng Làm việc Nhóm',
                'Hội thảo Quản lý Tài chính cá nhân',
                'Workshop Networking cho Sinh viên',
            ],
            'NGƯỜI TỐT' => [
                'Hoạt động Từ thiện vùng cao',
                'Chiến dịch Hiến máu Tình nguyện',
                'Ngày hội Môi trường Xanh',
                'Dự án Xây dựng Nhà tình thương',
                'Chiến dịch Mùa hè xanh',
                'Hoạt động Tình nguyện tại Trại trẻ',
                'Chương trình Tết cho người nghèo',
                'Hoạt động Dọn dẹp Bãi biển',
                'Chiến dịch Trồng cây Gây rừng',
                'Hoạt động Hỗ trợ Người già neo đơn',
            ],
            'THỂ THAO' => [
                'Giải đấu Bóng đá Sinh viên',
                'Giải đấu Vovinam mở rộng',
                'Giải Taekwondo sinh viên',
                'Chiến dịch Chạy bộ TVU Runner',
                'Giải đấu Cầu lông Mùa xuân',
                'Tournament Bóng chuyền',
                'Giải đấu Bóng rổ 3x3',
                'Marathon Sinh viên',
                'Giải đấu Cờ vua',
                'Hoạt động Yoga và Thiền',
            ],
            'VĂN HÓA' => [
                'Festival Nghệ thuật Khmer',
                'Ngày hội Văn hóa Dân tộc',
                'Triển lãm Nghệ thuật Sinh viên',
                'Chương trình Văn nghệ Truyền thống',
                'Đêm văn nghệ Sinh viên',
                'Hội thi Nấu ăn Dân gian',
                'Festival Áo dài',
                'Chương trình Giao lưu Văn hóa',
                'Triển lãm Ảnh Nghệ thuật',
                'Lễ hội Trăng Rằm',
            ],
            'HỌC THUẬT' => [
                'Hội thảo Nghiên cứu Khoa học Sinh viên',
                'Workshop Kỹ năng sống',
                'Hội thảo Truyền thông và Marketing',
                'Hội thảo Logistics và Thương mại điện tử',
                'Seminar Phương pháp Học tập hiệu quả',
                'English Speaking Day',
                'Workshop Kỹ năng Thuyết trình',
                'Hội thảo Quản lý Thời gian',
                'Seminar Hướng nghiệp',
                'Workshop Kỹ năng Lãnh đạo',
            ],
        ];

        foreach ($events as $event) {
            // Kiểm tra nếu tên hoạt động có dạng "Hoạt động X" hoặc "Hoạt đông X"
            if (preg_match('/Hoạt\s*(động|đông)\s+(\d+)/i', $event->title, $matches)) {
                $club = Club::find($event->club_id);
                if (!$club) continue;

                $clubName = strtoupper($club->name);
                $newTitle = null;

                // Tìm CLB phù hợp
                foreach ($clubActivities as $key => $activities) {
                    if (str_contains($clubName, $key)) {
                        $newTitle = $activities[array_rand($activities)];
                        break;
                    }
                }

                // Nếu không tìm thấy, dùng danh sách chung
                if (!$newTitle) {
                    $allActivities = array_merge(...array_values($clubActivities));
                    $newTitle = $allActivities[array_rand($allActivities)];
                }

                $event->title = $newTitle;
                $event->save();
                $updated++;
            }
        }

        $this->command->info("✅ Đã cập nhật {$updated} tên hoạt động.");
    }

    /**
     * Cập nhật creator (90 do chủ nhiệm/phó chủ nhiệm, 30 do sinh viên)
     */
    private function updateActivityCreators()
    {
        $this->command->info('👥 Cập nhật người tạo hoạt động...');

        $events = Event::all();
        $totalEvents = $events->count();
        $chairmanCount = (int)($totalEvents * 0.75); // 75% do chủ nhiệm/phó chủ nhiệm (gần 90/120)
        $studentCount = $totalEvents - $chairmanCount;

        $events = $events->shuffle();
        $updated = 0;

        foreach ($events->take($chairmanCount) as $event) {
            $club = Club::find($event->club_id);
            if (!$club) continue;

            // Lấy chủ nhiệm hoặc phó chủ nhiệm
            $chairmanOrVice = DB::table('club_members')
                ->where('club_id', $club->id)
                ->whereIn('position', ['chairman', 'vice_chairman'])
                ->where('status', 'approved')
                ->inRandomOrder()
                ->first();

            if ($chairmanOrVice) {
                $event->created_by = $chairmanOrVice->user_id;
                $event->approval_status = 'approved'; // Chủ nhiệm/phó chủ nhiệm tạo = approved
                // Đảm bảo status phù hợp với thời gian nếu đã approved
                $this->updateEventStatusByTime($event);
                $event->save();
                $updated++;
            }
        }

        // Còn lại là sinh viên đề xuất
        foreach ($events->skip($chairmanCount) as $event) {
            $club = Club::find($event->club_id);
            if (!$club) continue;

            // Lấy thành viên thường
            $member = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('position', 'member')
                ->where('status', 'approved')
                ->inRandomOrder()
                ->first();

            if ($member) {
                $event->created_by = $member->user_id;
                $event->approval_status = 'pending'; // Sinh viên đề xuất = pending
                // Nếu pending, luôn phải là upcoming (chưa được duyệt thì chưa diễn ra)
                $event->status = 'upcoming';
                $event->save();
                $updated++;
            }
        }

        $this->command->info("✅ Đã cập nhật {$updated} người tạo hoạt động.");
    }

    /**
     * Thêm dữ liệu đăng ký/tham gia
     */
    private function addEventRegistrations()
    {
        $this->command->info('📋 Thêm dữ liệu đăng ký/tham gia...');

        $events = Event::where('approval_status', 'approved')->get();
        $users = User::where('role_id', 2)->get();

        if ($events->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Không có hoạt động hoặc người dùng để tạo đăng ký.');
            return;
        }

        $added = 0;
        foreach ($events as $event) {
            // Mỗi hoạt động có ít nhất 5-50 người đăng ký
            $registrationCount = rand(5, 50);
            $selectedUsers = $users->shuffle()->take(min($registrationCount, $users->count()));

            foreach ($selectedUsers as $user) {
                // Kiểm tra xem đã đăng ký chưa
                $exists = DB::table('event_registrations')
                    ->where('event_id', $event->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if ($exists) continue;

                // Quyết định status: pending, approved, attended
                $rand = rand(1, 100);
                if ($rand <= 10) {
                    $status = 'pending';
                } elseif ($rand <= 30) {
                    $status = 'approved';
                } else {
                    $status = 'attended'; // 60% đã tham gia
                }

                DB::table('event_registrations')->insert([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'status' => $status,
                    'activity_points' => $status === 'attended' ? rand(1, 5) : 0,
                    'created_at' => Carbon::now()->subDays(rand(0, 30)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 30)),
                ]);
                $added++;
            }
        }

        $this->command->info("✅ Đã thêm {$added} đăng ký tham gia hoạt động.");
    }

    /**
     * Cập nhật status theo thời gian thực tế
     */
    private function updateActivityStatus()
    {
        $this->command->info('⏰ Cập nhật trạng thái theo thời gian...');

        $events = Event::all();
        $now = Carbon::now();
        $updated = 0;

        foreach ($events as $event) {
            if (!$event->start_at) continue;

            // Nếu đang chờ duyệt (pending), luôn phải là upcoming
            if ($event->approval_status === 'pending') {
                if ($event->status !== 'upcoming') {
                    $event->status = 'upcoming';
                    $event->save();
                    $updated++;
                }
                continue;
            }

            // Chỉ cập nhật status cho các hoạt động đã được duyệt
            if ($event->approval_status === 'approved') {
                $startAt = Carbon::parse($event->start_at);
                $endAt = $event->end_at ? Carbon::parse($event->end_at) : $startAt->copy()->addHours(3);

                $newStatus = 'upcoming';
                if ($startAt->isPast() && $endAt->isPast()) {
                    $newStatus = $event->status === 'cancelled' ? 'cancelled' : 'finished';
                } elseif ($startAt->isPast() && $endAt->isFuture()) {
                    $newStatus = 'ongoing';
                }

                if ($event->status !== $newStatus && $event->status !== 'disabled') {
                    $event->status = $newStatus;
                    $event->save();
                    $updated++;
                }
            }
        }

        $this->command->info("✅ Đã cập nhật {$updated} trạng thái hoạt động.");
    }

    /**
     * Cập nhật status của event dựa trên thời gian (chỉ cho event đã approved)
     */
    private function updateEventStatusByTime($event)
    {
        if (!$event->start_at || $event->approval_status !== 'approved') {
            return;
        }

        $startAt = Carbon::parse($event->start_at);
        $endAt = $event->end_at ? Carbon::parse($event->end_at) : $startAt->copy()->addHours(3);

        if ($startAt->isPast() && $endAt->isPast()) {
            $event->status = $event->status === 'cancelled' ? 'cancelled' : 'finished';
        } elseif ($startAt->isPast() && $endAt->isFuture()) {
            $event->status = 'ongoing';
        } else {
            $event->status = 'upcoming';
        }
    }
}

