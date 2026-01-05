<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use Carbon\Carbon;

class FixActivityStatusLogicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Bắt đầu sửa logic trạng thái hoạt động...');

        $events = Event::all();
        $fixed = 0;

        foreach ($events as $event) {
            $needsUpdate = false;

            $oldStatus = $event->status;

            // Logic: Nếu approval_status = 'pending', thì status phải là 'upcoming'
            if ($event->approval_status === 'pending') {
                if ($event->status !== 'upcoming' && $event->status !== 'disabled') {
                    $event->status = 'upcoming';
                    $needsUpdate = true;
                    $this->command->info("  Event ID {$event->id}: Sửa status từ '{$oldStatus}' thành 'upcoming' (vì đang pending)");
                }

                // Đảm bảo thời gian bắt đầu là tương lai nếu đang pending
                if ($event->start_at && Carbon::parse($event->start_at)->isPast()) {
                    $event->start_at = Carbon::now()->addDays(rand(1, 30));
                    if ($event->end_at) {
                        $event->end_at = Carbon::parse($event->start_at)->addHours(rand(2, 8));
                    }
                    $needsUpdate = true;
                    $this->command->info("  Event ID {$event->id}: Cập nhật thời gian sang tương lai (vì đang pending)");
                }
            } 
            // Chỉ cập nhật status cho các hoạt động đã được duyệt
            elseif ($event->approval_status === 'approved' && $event->start_at) {
                $startAt = Carbon::parse($event->start_at);
                $endAt = $event->end_at ? Carbon::parse($event->end_at) : $startAt->copy()->addHours(3);

                $correctStatus = 'upcoming';
                if ($startAt->isPast() && $endAt->isPast()) {
                    $correctStatus = $event->status === 'cancelled' ? 'cancelled' : 'finished';
                } elseif ($startAt->isPast() && $endAt->isFuture()) {
                    $correctStatus = 'ongoing';
                }

                if ($event->status !== $correctStatus && $event->status !== 'disabled') {
                    $event->status = $correctStatus;
                    $needsUpdate = true;
                    $this->command->info("  Event ID {$event->id}: Sửa status từ '{$oldStatus}' thành '{$correctStatus}' (theo thời gian)");
                }
            }

            if ($needsUpdate) {
                $event->save();
                $fixed++;
            }
        }

        $this->command->info("✅ Đã sửa logic cho {$fixed} hoạt động.");
    }
}

