<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Club;
use App\Models\User;
use App\Models\Event;
use App\Models\Violation;
use App\Models\Regulation;
use Carbon\Carbon;

class StatisticsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các CLB và User có sẵn
        $clubs = Club::where('status', 'active')->get();
        $users = User::where('role_id', 2)->get(); // Students
        
        if ($clubs->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Không có CLB hoặc User để tạo dữ liệu thống kê. Vui lòng chạy seeder khác trước.');
            return;
        }

        $this->command->info('Đang tạo dữ liệu thống kê...');

        // 1. Tạo thêm club_members nếu chưa có đủ
        $this->createClubMembers($clubs, $users);

        // 2. Tạo events (hoạt động) cho các CLB
        $this->createEvents($clubs, $users);

        // 3. Tạo event_registrations (đăng ký hoạt động)
        $this->createEventRegistrations();

        // 4. Tạo regulations (nội quy) nếu chưa có
        $this->createRegulations($clubs);

        // 5. Tạo violations (vi phạm)
        $this->createViolations($clubs, $users);

        $this->command->info('✅ Đã tạo dữ liệu thống kê thành công!');
    }

    private function createClubMembers($clubs, $users)
    {
        $this->command->info('Tạo thành viên CLB...');
        
        $existingCount = DB::table('club_members')->where('status', 'approved')->count();
        $targetCount = 30; // Mục tiêu có ít nhất 30 thành viên

        if ($existingCount >= $targetCount) {
            $this->command->info("Đã có {$existingCount} thành viên, bỏ qua.");
            return;
        }

        $positions = ['chairman', 'vice_chairman', 'member'];
        $added = 0;

        foreach ($clubs as $club) {
            $currentMembers = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('status', 'approved')
                ->count();

            // Mỗi CLB cần ít nhất 5-15 thành viên
            $needed = rand(5, 15) - $currentMembers;
            
            if ($needed <= 0) continue;

            $availableUsers = $users->shuffle()->take(min($needed, $users->count()));
            
            foreach ($availableUsers as $index => $user) {
                // Kiểm tra xem user đã là thành viên của CLB này chưa
                $exists = DB::table('club_members')
                    ->where('club_id', $club->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$exists) {
                    $position = $index === 0 ? 'chairman' : ($index === 1 ? 'vice_chairman' : 'member');
                    
                    DB::table('club_members')->insert([
                        'club_id' => $club->id,
                        'user_id' => $user->id,
                        'position' => $position,
                        'status' => 'approved',
                        'joined_date' => Carbon::now()->subDays(rand(1, 365)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $added++;
                }
            }
        }

        $this->command->info("Đã thêm {$added} thành viên CLB.");
    }

    private function createEvents($clubs, $users)
    {
        $this->command->info('Tạo hoạt động/sự kiện...');
        
        $existingCount = Event::where('approval_status', 'approved')->count();
        $targetCount = 50; // Mục tiêu có ít nhất 50 hoạt động

        if ($existingCount >= $targetCount) {
            $this->command->info("Đã có {$existingCount} hoạt động, bỏ qua.");
            return;
        }

        // Kiểm tra enum values từ database
        $activityTypes = ['academic', 'arts', 'volunteer', 'other'];
        $statuses = ['upcoming', 'ongoing', 'finished', 'cancelled'];
        $approvalStatuses = ['approved', 'pending', 'rejected'];
        
        $added = 0;
        $needed = $targetCount - $existingCount;

        for ($i = 0; $i < $needed; $i++) {
            $club = $clubs->random();
            $creator = $users->random();
            
            // Tạo thời gian trong 12 tháng qua và tương lai
            $startAt = Carbon::now()->subMonths(rand(0, 11))->addDays(rand(-30, 60));
            $endAt = $startAt->copy()->addHours(rand(2, 8));
            
            // Xác định status dựa trên thời gian
            $status = 'upcoming';
            if ($startAt->isPast() && $endAt->isPast()) {
                $status = rand(0, 10) < 1 ? 'cancelled' : 'finished';
            } elseif ($startAt->isPast() && $endAt->isFuture()) {
                $status = 'ongoing';
            }

            $approvalStatus = rand(0, 10) < 1 ? 'pending' : (rand(0, 10) < 1 ? 'rejected' : 'approved');
            
            Event::create([
                'title' => 'Hoạt động ' . ($i + 1) . ' - ' . $club->name,
                'club_id' => $club->id,
                'description' => 'Mô tả chi tiết về hoạt động này. Đây là một hoạt động thú vị và bổ ích.',
                'activity_type' => $activityTypes[array_rand($activityTypes)],
                'start_at' => $startAt,
                'end_at' => $endAt,
                'location' => 'Phòng ' . rand(101, 999) . ', Tòa nhà ' . chr(65 + rand(0, 5)),
                'status' => $status,
                'approval_status' => $approvalStatus,
                'created_by' => $creator->id,
                'expected_participants' => rand(20, 100),
                'expected_budget' => rand(500000, 5000000),
                'goal' => 'Mục tiêu của hoạt động này là...',
                'created_at' => $startAt->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
            $added++;
        }

        $this->command->info("Đã thêm {$added} hoạt động.");
    }

    private function createEventRegistrations()
    {
        $this->command->info('Tạo đăng ký tham gia hoạt động...');
        
        $events = Event::where('approval_status', 'approved')->get();
        $users = User::where('role_id', 2)->get();
        
        if ($events->isEmpty() || $users->isEmpty()) {
            return;
        }

        $added = 0;
        foreach ($events as $event) {
            // Mỗi hoạt động có 10-50 người đăng ký
            $participants = $users->shuffle()->take(rand(10, min(50, $users->count())));
            
            foreach ($participants as $user) {
                // Kiểm tra xem đã đăng ký chưa
                $exists = DB::table('event_registrations')
                    ->where('event_id', $event->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$exists) {
                    $statuses = ['pending', 'approved', 'rejected', 'attended', 'absent'];
                    $status = $statuses[array_rand($statuses)];
                    
                    DB::table('event_registrations')->insert([
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'status' => $status,
                        'activity_points' => $status === 'attended' ? rand(5, 20) : 0,
                        'created_at' => Carbon::now()->subDays(rand(1, 30)),
                        'updated_at' => now(),
                    ]);
                    $added++;
                }
            }
        }

        $this->command->info("Đã thêm {$added} đăng ký hoạt động.");
    }

    private function createRegulations($clubs)
    {
        $this->command->info('Tạo nội quy...');
        
        $existingCount = Regulation::count();
        if ($existingCount >= 10) {
            $this->command->info("Đã có {$existingCount} nội quy, bỏ qua.");
            return;
        }

        // Kiểm tra enum values từ migration
        $scopes = ['all_clubs', 'specific_club'];
        $severities = ['light', 'medium', 'serious'];
        $statuses = ['active', 'inactive'];

        $added = 0;
        $needed = 10 - $existingCount;

        for ($i = 0; $i < $needed; $i++) {
            $scope = $scopes[array_rand($scopes)];
            $clubId = $scope === 'specific_club' ? $clubs->random()->id : null;
            
            Regulation::create([
                'code' => 'NQ-' . str_pad($existingCount + $i + 1, 3, '0', STR_PAD_LEFT),
                'title' => 'Nội quy ' . ($existingCount + $i + 1),
                'content' => 'Nội dung chi tiết của nội quy này. Các thành viên cần tuân thủ nghiêm ngặt.',
                'scope' => $scope,
                'club_id' => $clubId,
                'severity' => $severities[array_rand($severities)],
                'status' => $statuses[array_rand($statuses)],
                'issued_date' => Carbon::now()->subDays(rand(1, 365)),
                'created_by' => 1, // Admin
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $added++;
        }

        $this->command->info("Đã thêm {$added} nội quy.");
    }

    private function createViolations($clubs, $users)
    {
        $this->command->info('Tạo vi phạm...');
        
        $existingCount = Violation::count();
        if ($existingCount >= 20) {
            $this->command->info("Đã có {$existingCount} vi phạm, bỏ qua.");
            return;
        }

        $regulations = Regulation::where('status', 'active')->get();
        $clubMembers = DB::table('club_members')
            ->where('status', 'approved')
            ->get();

        if ($regulations->isEmpty() || $clubMembers->isEmpty()) {
            return;
        }

        $severities = ['light', 'medium', 'serious'];
        $statuses = ['pending', 'processed', 'monitoring'];
        $disciplineTypes = ['warning', 'reprimand', 'suspension', 'expulsion', null];

        $added = 0;
        $needed = 20 - $existingCount;

        for ($i = 0; $i < $needed; $i++) {
            $member = $clubMembers->random();
            $club = $clubs->find($member->club_id);
            $regulation = $regulations->random();
            $user = $users->find($member->user_id);
            
            if (!$club || !$user) continue;

            $status = $statuses[array_rand($statuses)];
            $disciplineType = $status === 'processed' ? $disciplineTypes[array_rand($disciplineTypes)] : null;
            
            $violation = Violation::create([
                'user_id' => $user->id,
                'club_id' => $club->id,
                'regulation_id' => $regulation->id,
                'description' => 'Mô tả vi phạm: ' . $user->name . ' đã vi phạm nội quy ' . $regulation->title,
                'severity' => $severities[array_rand($severities)],
                'violation_date' => Carbon::now()->subDays(rand(1, 180)),
                'recorded_by' => $club->owner_id ?? 1,
                'status' => $status,
                'discipline_type' => $disciplineType,
                'discipline_reason' => $disciplineType ? 'Lý do xử lý kỷ luật...' : null,
                'discipline_period_start' => $disciplineType ? Carbon::now()->subDays(rand(1, 30)) : null,
                'discipline_period_end' => $disciplineType ? Carbon::now()->addDays(rand(30, 90)) : null,
                'processed_by' => $status === 'processed' ? 1 : null, // Admin
                'processed_at' => $status === 'processed' ? Carbon::now()->subDays(rand(1, 30)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $added++;
        }

        $this->command->info("Đã thêm {$added} vi phạm.");
    }
}

