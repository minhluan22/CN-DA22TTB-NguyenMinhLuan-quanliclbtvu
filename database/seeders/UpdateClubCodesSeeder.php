<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use Illuminate\Support\Facades\DB;

class UpdateClubCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Cập nhật mã CLB từ format cũ (CLB-XXX) sang format mới (CLB047, CLB048...)
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu cập nhật mã CLB sang format mới (CLB047, CLB048...)...');

        // Lấy tất cả CLB chưa có format CLB + số (bỏ qua các mã như CLB047, CLB048...)
        $clubsToUpdate = Club::whereNotNull('code')
            ->where('code', 'like', 'CLB%')
            ->whereRaw("code NOT REGEXP '^CLB[0-9]+$'")
            ->orWhere(function($q) {
                $q->whereNotNull('code')
                  ->where('code', 'not like', 'CLB%');
            })
            ->get();

        if ($clubsToUpdate->isEmpty()) {
            $this->command->info('✅ Không có CLB nào cần cập nhật!');
            return;
        }

        // Lấy số cao nhất hiện có (format CLB + số)
        $maxNumber = Club::whereNotNull('code')
            ->where('code', 'like', 'CLB%')
            ->whereRaw("code REGEXP '^CLB[0-9]+$'")
            ->get()
            ->map(function($club) {
                if (preg_match('/^CLB(\d+)$/', $club->code, $m)) {
                    return intval($m[1]);
                }
                return 0;
            })
            ->filter(function($num) {
                return $num >= 47;
            })
            ->max() ?? 46; // Bắt đầu từ 46, nếu không có thì sẽ bắt đầu từ 47

        $nextNumber = max(47, $maxNumber + 1);
        $updated = 0;
        $skipped = 0;

        foreach ($clubsToUpdate as $club) {
            $newCode = 'CLB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            // Kiểm tra mã mới đã tồn tại chưa
            $codeExists = Club::where('code', $newCode)
                             ->where('id', '!=', $club->id)
                             ->exists();

            if ($codeExists) {
                $this->command->warn("⚠️  Mã {$newCode} đã tồn tại, bỏ qua CLB ID: {$club->id}");
                $skipped++;
                continue;
            }

            $oldCode = $club->code;
            $club->code = $newCode;
            $club->save();
            
            $this->command->info("✅ Đã cập nhật: {$oldCode} → {$newCode} (CLB: {$club->name})");
            $updated++;
            $nextNumber++;
        }

        $this->command->info("✅ Đã cập nhật {$updated} mã CLB thành công!");
        if ($skipped > 0) {
            $this->command->warn("⚠️  Đã bỏ qua {$skipped} CLB do mã trùng.");
        }
    }
}

