<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Club;

class UpdatePositionLimitsSeeder extends Seeder
{
    /**
     * Giới hạn số lượng cho mỗi chức vụ
     */
    private function getPositionLimit(string $position): ?int
    {
        $limits = [
            'chairman' => 1,
            'vice_chairman' => 2,
            'secretary' => 1,
            'head_expertise' => 1,
            'head_media' => 1,
            'head_events' => 1,
            'treasurer' => 1,
            'member' => null, // Không giới hạn
        ];
        
        return $limits[$position] ?? null;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu cập nhật số lượng chức vụ theo giới hạn...');

        $clubs = Club::all();
        $totalUpdated = 0;

        foreach ($clubs as $club) {
            $updated = $this->updateClubPositions($club->id);
            $totalUpdated += $updated;
        }

        $this->command->info("✅ Đã cập nhật {$totalUpdated} thành viên trong {$clubs->count()} CLB.");
    }

    /**
     * Cập nhật số lượng chức vụ cho một CLB
     */
    private function updateClubPositions(int $clubId): int
    {
        $updated = 0;
        
        // Danh sách các chức vụ cần kiểm tra (không bao gồm member)
        $positions = [
            'chairman',
            'vice_chairman',
            'secretary',
            'head_expertise',
            'head_media',
            'head_events',
            'treasurer'
        ];

        foreach ($positions as $position) {
            $limit = $this->getPositionLimit($position);
            if ($limit === null) continue;

            // Lấy danh sách thành viên có chức vụ này (chỉ tính approved)
            $members = DB::table('club_members')
                ->where('club_id', $clubId)
                ->where('position', $position)
                ->where('status', 'approved')
                ->orderBy('joined_date', 'asc') // Giữ lại những người tham gia sớm nhất
                ->get();

            $currentCount = $members->count();

            if ($currentCount > $limit) {
                // Có quá nhiều người, chuyển các người thừa thành thành viên
                $excess = $currentCount - $limit;
                $toUpdate = $members->skip($limit)->take($excess);

                foreach ($toUpdate as $member) {
                    DB::table('club_members')
                        ->where('id', $member->id)
                        ->update([
                            'position' => 'member',
                            'updated_at' => now(),
                        ]);
                    $updated++;
                }

                $this->command->info("  CLB ID {$clubId}: Chuyển {$excess} người từ {$position} thành thành viên");
            }
        }

        // Thêm các chức vụ còn thiếu
        $this->addMissingPositions($clubId, $updated);

        return $updated;
    }

    /**
     * Thêm các chức vụ còn thiếu
     */
    private function addMissingPositions(int $clubId, int &$updated): void
    {
        // Lấy danh sách ID của thành viên đã approved nhưng chưa có chức vụ cụ thể
        $availableMemberIds = DB::table('club_members')
            ->where('club_id', $clubId)
            ->where('position', 'member')
            ->where('status', 'approved')
            ->orderBy('joined_date', 'asc')
            ->pluck('id')
            ->toArray();

        if (empty($availableMemberIds)) {
            return;
        }

        $positionsToAdd = [
            'secretary' => 1,
            'head_expertise' => 1,
            'head_media' => 1,
            'head_events' => 1,
            'treasurer' => 1,
        ];

        $memberIndex = 0;

        foreach ($positionsToAdd as $position => $limit) {
            // Kiểm tra xem đã có chức vụ này chưa
            $existing = DB::table('club_members')
                ->where('club_id', $clubId)
                ->where('position', $position)
                ->where('status', 'approved')
                ->count();

            if ($existing >= $limit) {
                continue; // Đã đủ
            }

            // Cần thêm bao nhiêu người
            $needed = $limit - $existing;

            // Kiểm tra còn đủ thành viên không
            if ($memberIndex >= count($availableMemberIds)) {
                break; // Hết thành viên để thêm
            }

            // Lấy số lượng thành viên cần thêm
            $membersToPromote = array_slice($availableMemberIds, $memberIndex, $needed);

            foreach ($membersToPromote as $memberId) {
                DB::table('club_members')
                    ->where('id', $memberId)
                    ->update([
                        'position' => $position,
                        'updated_at' => now(),
                    ]);
                $updated++;
                $memberIndex++;
            }

            if ($needed > 0 && count($membersToPromote) > 0) {
                $this->command->info("  CLB ID {$clubId}: Thêm " . count($membersToPromote) . " người vào chức vụ {$position}");
            }
        }
    }
}

