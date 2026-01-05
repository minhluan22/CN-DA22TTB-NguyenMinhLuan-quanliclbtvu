<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Club;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;

class DistributeAllDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu phân bố đều tất cả dữ liệu...');

        // 1. Phân bố thành viên CLB
        $this->distributeClubMembers();

        // 2. Phân bố hoạt động
        $this->distributeActivities();

        // 3. Phân bố đăng ký/tham gia hoạt động
        $this->distributeEventRegistrations();

        // 4. Phân bố vi phạm/kỷ luật
        $this->distributeViolations();

        $this->command->info('✅ Hoàn thành phân bố đều tất cả dữ liệu!');
    }

    /**
     * Phân bố đều thành viên CLB cho tất cả sinh viên
     */
    private function distributeClubMembers()
    {
        $this->command->info('👥 Phân bố thành viên CLB...');

        $clubs = Club::where('status', 'active')->get();
        $students = User::where('role_id', 2)
            ->whereNotNull('student_code')
            ->whereNotNull('department')
            ->get();

        if ($clubs->isEmpty() || $students->isEmpty()) {
            $this->command->warn('Không có CLB hoặc sinh viên để phân bố.');
            return;
        }

        // Mapping lĩnh vực CLB -> Khoa liên quan
        $clubFieldToFaculties = [
            'Công nghệ' => ['Khoa Công nghệ Thông tin', 'Khoa Kỹ thuật và Công nghệ'],
            'Kinh tế' => ['Khoa Kinh tế - Luật'],
            'Y tế' => ['Khoa Y Dược'],
            'Ngoại ngữ' => ['Khoa Ngoại ngữ'],
            'Nghệ thuật' => ['Khoa Khoa học Xã hội và Nhân văn'],
            'Tình nguyện' => ['all'],
            'Học tập' => ['all'],
            'Kỹ năng' => ['all'],
            'Truyền thông' => ['Khoa Khoa học Xã hội và Nhân văn', 'Khoa Công nghệ Thông tin'],
            'Sáng tạo' => ['Khoa Khoa học Xã hội và Nhân văn', 'Khoa Công nghệ Thông tin'],
            'Thể thao' => ['all'],
            'Môi trường' => ['all'],
            'Khoa học' => ['all'],
            'Kinh doanh' => ['Khoa Kinh tế - Luật'],
        ];

        // Xóa tất cả thành viên CLB cũ (trừ chủ nhiệm và phó chủ nhiệm)
        DB::table('club_members')
            ->whereNotIn('position', ['chairman', 'vice_chairman'])
            ->delete();

        $totalAdded = 0;
        $targetMembersPerClub = 40; // Mỗi CLB có khoảng 40 thành viên để chia đều

        foreach ($clubs as $club) {
            $this->command->info("  📋 CLB: {$club->name}");

            // Lấy thành viên hiện tại (chủ nhiệm, phó chủ nhiệm)
            $existingMemberIds = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('status', 'approved')
                ->pluck('user_id')
                ->toArray();

            // Xác định khoa liên quan
            $relatedFaculties = $clubFieldToFaculties[$club->field] ?? ['all'];
            $isAllFaculties = in_array('all', $relatedFaculties);

            // Tính số lượng thành viên cần thêm
            $neededCount = max(0, $targetMembersPerClub - count($existingMemberIds));

            // 70% thành viên liên quan đến khoa, 30% lộn xộn
            $relatedCount = (int)($neededCount * 0.7);
            $randomCount = $neededCount - $relatedCount;

            // Lọc sinh viên theo khoa liên quan (70%)
            $availableStudents = $students->whereNotIn('id', $existingMemberIds);
            
            if ($isAllFaculties) {
                $relatedStudents = $availableStudents->shuffle()->take($relatedCount);
            } else {
                $relatedStudents = $availableStudents->filter(function($student) use ($relatedFaculties) {
                    foreach ($relatedFaculties as $faculty) {
                        if (stripos($student->department, $faculty) !== false) {
                            return true;
                        }
                    }
                    return false;
                })->shuffle()->take($relatedCount);
            }

            // Lấy sinh viên lộn xộn (30%)
            $randomStudents = $availableStudents
                ->whereNotIn('id', $relatedStudents->pluck('id'))
                ->shuffle()
                ->take($randomCount);

            $selectedStudents = $relatedStudents->merge($randomStudents);

            // Đếm số lượng chức vụ hiện tại
            $positionCounts = [
                'chairman' => DB::table('club_members')->where('club_id', $club->id)->where('position', 'chairman')->count(),
                'vice_chairman' => DB::table('club_members')->where('club_id', $club->id)->where('position', 'vice_chairman')->count(),
                'secretary' => 0,
                'head_expertise' => 0,
                'head_media' => 0,
                'head_events' => 0,
                'treasurer' => 0,
                'member' => 0,
            ];

            // Gán thành viên
            foreach ($selectedStudents as $student) {
                // Xác định chức vụ
                $position = 'member';
                if ($positionCounts['secretary'] < 1) {
                    $position = 'secretary';
                    $positionCounts['secretary']++;
                } elseif ($positionCounts['head_expertise'] < 1) {
                    $position = 'head_expertise';
                    $positionCounts['head_expertise']++;
                } elseif ($positionCounts['head_media'] < 1) {
                    $position = 'head_media';
                    $positionCounts['head_media']++;
                } elseif ($positionCounts['head_events'] < 1) {
                    $position = 'head_events';
                    $positionCounts['head_events']++;
                } elseif ($positionCounts['treasurer'] < 1) {
                    $position = 'treasurer';
                    $positionCounts['treasurer']++;
                } else {
                    $position = 'member';
                    $positionCounts['member']++;
                }

                DB::table('club_members')->insert([
                    'club_id' => $club->id,
                    'user_id' => $student->id,
                    'position' => $position,
                    'status' => 'approved',
                    'joined_date' => Carbon::now()->subDays(rand(1, 365)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $totalAdded++;
            }
        }

        $this->command->info("✅ Đã phân bố {$totalAdded} thành viên CLB.");
    }

    /**
     * Phân bố đều hoạt động cho các CLB
     */
    private function distributeActivities()
    {
        $this->command->info('📅 Phân bố hoạt động...');

        $clubs = Club::where('status', 'active')->get();
        $students = User::where('role_id', 2)->get();

        if ($clubs->isEmpty() || $students->isEmpty()) {
            $this->command->warn('Không có CLB hoặc sinh viên để tạo hoạt động.');
            return;
        }

        // Mục tiêu: Mỗi CLB có khoảng 8-12 hoạt động
        $targetEventsPerClub = 10;
        $totalClubs = $clubs->count();
        $targetTotalEvents = $totalClubs * $targetEventsPerClub;

        // Lấy số lượng hoạt động hiện tại
        $currentEventsCount = Event::count();

        // Tính số lượng hoạt động cần tạo thêm
        $neededEvents = max(0, $targetTotalEvents - $currentEventsCount);

        if ($neededEvents > 0) {
            // Phân bố đều cho các CLB
            $eventsPerClub = (int)ceil($neededEvents / $totalClubs);

            $activityTemplates = [
                ['title' => 'Workshop chuyên đề', 'type' => 'academic'],
                ['title' => 'Hoạt động tình nguyện', 'type' => 'volunteer'],
                ['title' => 'Giải đấu thể thao', 'type' => 'sports'],
                ['title' => 'Biểu diễn nghệ thuật', 'type' => 'arts'],
                ['title' => 'Hội thảo chia sẻ', 'type' => 'academic'],
                ['title' => 'Chiến dịch xã hội', 'type' => 'volunteer'],
                ['title' => 'Cuộc thi tài năng', 'type' => 'arts'],
                ['title' => 'Training kỹ năng', 'type' => 'academic'],
            ];

            $added = 0;
            foreach ($clubs as $club) {
                for ($i = 0; $i < $eventsPerClub && $added < $neededEvents; $i++) {
                    $template = $activityTemplates[array_rand($activityTemplates)];
                    $creator = $students->random();

                    // Tạo thời gian trong 12 tháng qua và tương lai
                    $startAt = Carbon::now()->subMonths(rand(0, 11))->addDays(rand(-30, 90));
                    $endAt = $startAt->copy()->addHours(rand(2, 8));

                    $status = 'upcoming';
                    if ($startAt->isPast() && $endAt->isPast()) {
                        $status = 'finished';
                    } elseif ($startAt->isPast() && $endAt->isFuture()) {
                        $status = 'ongoing';
                    }

                    // 75% từ chủ nhiệm/phó chủ nhiệm, 25% từ thành viên
                    $isLeader = rand(1, 100) <= 75;
                    $approvalStatus = $isLeader ? 'approved' : (rand(1, 10) < 3 ? 'pending' : 'approved');
                    if ($approvalStatus === 'pending') {
                        $status = 'upcoming';
                    }

                    Event::create([
                        'club_id' => $club->id,
                        'title' => $template['title'] . ' - ' . $club->name,
                        'description' => 'Hoạt động của ' . $club->name,
                        'location' => 'Địa điểm tổ chức',
                        'start_at' => $startAt,
                        'end_at' => $endAt,
                        'status' => $status,
                        'approval_status' => $approvalStatus,
                        'created_by' => $creator->id,
                        'created_at' => $startAt->copy()->subDays(rand(1, 30)),
                        'updated_at' => now(),
                    ]);

                    $added++;
                }
            }

            $this->command->info("✅ Đã tạo thêm {$added} hoạt động.");
        } else {
            $this->command->info("✅ Đã đủ hoạt động ({$currentEventsCount} hoạt động).");
        }
    }

    /**
     * Phân bố đều đăng ký/tham gia hoạt động
     */
    private function distributeEventRegistrations()
    {
        $this->command->info('📋 Phân bố đăng ký/tham gia hoạt động...');

        $events = Event::where('approval_status', 'approved')->get();
        $students = User::where('role_id', 2)->get();

        if ($events->isEmpty() || $students->isEmpty()) {
            $this->command->warn('Không có hoạt động hoặc sinh viên để tạo đăng ký.');
            return;
        }

        // Xóa tất cả đăng ký cũ
        DB::table('event_registrations')->truncate();

        $totalAdded = 0;
        $targetRegistrationsPerEvent = 30; // Mỗi hoạt động có khoảng 30 người đăng ký

        foreach ($events as $event) {
            // Chọn ngẫu nhiên sinh viên để đăng ký
            $selectedStudents = $students->shuffle()->take(min($targetRegistrationsPerEvent, $students->count()));

            foreach ($selectedStudents as $student) {
                // Status: 60% attended, 20% approved (chưa tham gia), 15% pending, 5% rejected
                $rand = rand(1, 100);
                if ($rand <= 60) {
                    $status = 'attended';
                    $activityPoints = rand(5, 20);
                } elseif ($rand <= 80) {
                    $status = 'approved';
                    $activityPoints = 0;
                } elseif ($rand <= 95) {
                    $status = 'pending';
                    $activityPoints = 0;
                } else {
                    $status = 'rejected';
                    $activityPoints = 0;
                }

                DB::table('event_registrations')->insert([
                    'event_id' => $event->id,
                    'user_id' => $student->id,
                    'status' => $status,
                    'activity_points' => $activityPoints,
                    'created_at' => Carbon::parse($event->created_at)->addDays(rand(0, 5)),
                    'updated_at' => now(),
                ]);

                $totalAdded++;
            }
        }

        $this->command->info("✅ Đã phân bố {$totalAdded} đăng ký/tham gia hoạt động.");
    }

    /**
     * Phân bố đều vi phạm/kỷ luật
     */
    private function distributeViolations()
    {
        $this->command->info('⚠️ Phân bố vi phạm/kỷ luật...');

        $events = Event::where('approval_status', 'approved')
            ->whereNull('violation_type')
            ->get();

        if ($events->isEmpty()) {
            $this->command->warn('Không có hoạt động để đánh dấu vi phạm.');
            return;
        }

        // Lấy admin user
        $admin = DB::table('users')->where('role_id', 1)->first();
        if (!$admin) {
            $this->command->warn('Không tìm thấy Admin user.');
            return;
        }

        // Mục tiêu: 10-15% hoạt động có vi phạm
        $targetViolationCount = (int)($events->count() * 0.12); // 12%
        $selectedEvents = $events->shuffle()->take(min($targetViolationCount, $events->count()));

        $violationTypes = [
            'Tổ chức không đúng nội dung đã đăng ký',
            'Vi phạm nội quy CLB',
            'Vi phạm nội quy nhà trường',
            'Không xin phép nhưng vẫn tổ chức',
            'Tổ chức sai thời gian/địa điểm',
            'Có phản ánh từ sinh viên',
            'Nội dung không phù hợp',
            'Vi phạm quy định về tài chính',
        ];

        $added = 0;
        foreach ($selectedEvents as $event) {
            // Mức độ vi phạm: 50% nhẹ, 35% trung bình, 15% nghiêm trọng
            $severityRand = rand(1, 100);
            if ($severityRand <= 50) {
                $severity = 'light';
            } elseif ($severityRand <= 85) {
                $severity = 'medium';
            } else {
                $severity = 'serious';
            }

            // Trạng thái xử lý: 40% pending, 35% processing, 25% processed
            $statusRand = rand(1, 100);
            if ($statusRand <= 40) {
                $violationStatus = 'pending';
            } elseif ($statusRand <= 75) {
                $violationStatus = 'processing';
            } else {
                $violationStatus = 'processed';
            }

            $violationType = $violationTypes[array_rand($violationTypes)];
            $violationDetectedAt = Carbon::parse($event->created_at)->addDays(rand(1, 7));

            // Tạo ghi chú vi phạm
            $violationNotes = "Loại vi phạm: {$violationType}\n";
            $violationNotes .= "Mức độ: " . ($severity === 'light' ? 'Nhẹ' : ($severity === 'medium' ? 'Trung bình' : 'Nghiêm trọng')) . "\n";
            $violationNotes .= "Mô tả: Hoạt động đã vi phạm quy định và đang được xử lý.";

            $event->update([
                'violation_type' => $violationType,
                'violation_severity' => $severity,
                'violation_status' => $violationStatus,
                'violation_notes' => $violationNotes,
                'violation_detected_at' => $violationDetectedAt,
                'violation_recorded_by' => $admin->id,
                'status' => $severity === 'serious' ? 'disabled' : $event->status,
                'updated_at' => now(),
            ]);

            $added++;
        }

        $this->command->info("✅ Đã phân bố {$added} vi phạm/kỷ luật.");
    }
}

