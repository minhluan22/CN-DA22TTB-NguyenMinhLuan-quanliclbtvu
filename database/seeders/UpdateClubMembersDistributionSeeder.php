<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Club;
use App\Models\User;
use Carbon\Carbon;

class UpdateClubMembersDistributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu cập nhật phân bố thành viên CLB...');

        // Mapping lĩnh vực CLB -> Khoa/Ngành liên quan
        $clubFieldToFaculties = [
            'Công nghệ' => [
                'Khoa Công nghệ Thông tin',
                'Khoa Kỹ thuật và Công nghệ',
            ],
            'Kinh tế' => [
                'Khoa Kinh tế - Luật',
            ],
            'Y tế' => [
                'Khoa Y Dược',
            ],
            'Ngoại ngữ' => [
                'Khoa Ngoại ngữ',
            ],
            'Nghệ thuật' => [
                'Khoa Khoa học Xã hội và Nhân văn',
            ],
            'Tình nguyện' => [
                // Tình nguyện có thể từ mọi khoa
                'all'
            ],
            'Học tập' => [
                // Học tập có thể từ mọi khoa
                'all'
            ],
            'Kỹ năng' => [
                // Kỹ năng có thể từ mọi khoa
                'all'
            ],
            'Truyền thông' => [
                'Khoa Khoa học Xã hội và Nhân văn',
                'Khoa Công nghệ Thông tin',
            ],
            'Sáng tạo' => [
                'Khoa Khoa học Xã hội và Nhân văn',
                'Khoa Công nghệ Thông tin',
            ],
        ];

        $clubs = Club::where('status', 'active')->get();
        $students = User::where('role_id', 2)
            ->whereNotNull('department')
            ->whereNotNull('student_code')
            ->get();

        $totalUpdated = 0;
        $totalRemoved = 0;

        foreach ($clubs as $club) {
            $this->command->info("  📋 Xử lý CLB: {$club->name} ({$club->field})");

            // Xác định khoa liên quan
            $relatedFaculties = $clubFieldToFaculties[$club->field] ?? ['all'];
            $isAllFaculties = in_array('all', $relatedFaculties);

            // Lấy thành viên hiện tại
            $currentMembers = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('status', 'approved')
                ->pluck('user_id')
                ->toArray();

            // Xác định số lượng thành viên mục tiêu
            $targetCount = rand(15, 35); // Mỗi CLB có 15-35 thành viên

            // 70% thành viên liên quan đến khoa/ngành, 30% lộn xộn
            $relatedCount = (int)($targetCount * 0.7);
            $randomCount = $targetCount - $relatedCount;

            // Lọc sinh viên theo khoa liên quan (70%)
            $relatedStudents = collect();
            if ($isAllFaculties) {
                // Nếu CLB chấp nhận mọi khoa, lấy ngẫu nhiên
                $relatedStudents = $students->shuffle()->take($relatedCount);
            } else {
                // Lọc theo khoa liên quan
                $relatedStudents = $students->filter(function($student) use ($relatedFaculties) {
                    foreach ($relatedFaculties as $faculty) {
                        if (stripos($student->department, $faculty) !== false) {
                            return true;
                        }
                    }
                    return false;
                })->shuffle()->take($relatedCount);
            }

            // Lấy sinh viên lộn xộn (30%)
            $randomStudents = $students
                ->whereNotIn('id', $relatedStudents->pluck('id'))
                ->shuffle()
                ->take($randomCount);

            // Gộp danh sách
            $selectedStudents = $relatedStudents->merge($randomStudents);

            // Xóa thành viên cũ không còn phù hợp (giữ lại chủ nhiệm và phó chủ nhiệm)
            $keepPositions = ['chairman', 'vice_chairman'];
            $membersToKeep = DB::table('club_members')
                ->where('club_id', $club->id)
                ->whereIn('position', $keepPositions)
                ->pluck('user_id')
                ->toArray();

            $membersToRemove = array_diff($currentMembers, $membersToKeep, $selectedStudents->pluck('id')->toArray());
            
            if (!empty($membersToRemove)) {
                DB::table('club_members')
                    ->where('club_id', $club->id)
                    ->whereIn('user_id', $membersToRemove)
                    ->whereNotIn('position', $keepPositions)
                    ->delete();
                $totalRemoved += count($membersToRemove);
            }

            // Thêm thành viên mới
            $added = 0;
            $positionCounts = [
                'chairman' => 0,
                'vice_chairman' => 0,
                'secretary' => 0,
                'head_expertise' => 0,
                'head_media' => 0,
                'head_events' => 0,
                'treasurer' => 0,
                'member' => 0,
            ];

            // Đếm số lượng chức vụ hiện tại
            $existingPositions = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('status', 'approved')
                ->pluck('position')
                ->toArray();

            foreach ($existingPositions as $pos) {
                if (isset($positionCounts[$pos])) {
                    $positionCounts[$pos]++;
                }
            }

            foreach ($selectedStudents as $index => $student) {
                // Kiểm tra đã là thành viên chưa
                $exists = DB::table('club_members')
                    ->where('club_id', $club->id)
                    ->where('user_id', $student->id)
                    ->exists();

                if ($exists) {
                    continue; // Đã là thành viên, bỏ qua
                }

                // Xác định chức vụ
                $position = 'member';
                
                // Chỉ gán chức vụ nếu chưa đạt giới hạn
                if ($positionCounts['chairman'] < 1) {
                    $position = 'chairman';
                    $positionCounts['chairman']++;
                } elseif ($positionCounts['vice_chairman'] < 2) {
                    $position = 'vice_chairman';
                    $positionCounts['vice_chairman']++;
                } elseif ($positionCounts['secretary'] < 1) {
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

                $added++;
            }

            $totalUpdated += $added;
            $this->command->info("    ✅ CLB {$club->name}: Thêm {$added} thành viên, Xóa " . count($membersToRemove) . " thành viên");
        }

        $this->command->info("✅ Đã cập nhật phân bố thành viên CLB:");
        $this->command->info("   - Thêm mới: {$totalUpdated} thành viên");
        $this->command->info("   - Xóa: {$totalRemoved} thành viên");
        $this->command->info("   - 70% thành viên liên quan đến khoa/ngành của CLB");
        $this->command->info("   - 30% thành viên lộn xộn (đa dạng)");
    }
}

