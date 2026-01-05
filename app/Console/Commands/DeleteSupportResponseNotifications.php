<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\NotificationRecipient;

class DeleteSupportResponseNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:delete-support-responses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa các thông báo phản hồi hỗ trợ cũ (gửi toàn hệ thống)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang tìm các thông báo phản hồi hỗ trợ cũ...');

        // Tìm các notification có title "Phản hồi yêu cầu hỗ trợ"
        // và có target_type là 'all' hoặc 'students' (gửi toàn hệ thống)
        // hoặc is_public = true (hiển thị công khai)
        $notifications = Notification::where('title', 'Phản hồi yêu cầu hỗ trợ')
            ->where(function($q) {
                $q->where(function($subQ) {
                    $subQ->where('target_type', 'all')
                         ->orWhere('target_type', 'students');
                })
                ->orWhere('is_public', true)
                ->orWhereNull('is_public');
            })
            ->get();

        $count = $notifications->count();
        
        if ($count === 0) {
            $this->info('Không tìm thấy thông báo nào cần xóa.');
            return 0;
        }

        $this->warn("Tìm thấy {$count} thông báo cần xóa:");
        
        foreach ($notifications as $notification) {
            $this->line("  - ID: {$notification->id}, Title: {$notification->title}, Target: {$notification->target_type}, Public: " . ($notification->is_public ? 'true' : 'false'));
        }

        if ($this->confirm('Bạn có chắc chắn muốn xóa các thông báo này?', true)) {
            $deleted = 0;
            
            foreach ($notifications as $notification) {
                // Xóa notification sẽ tự động xóa notification_recipients (cascade)
                $notification->delete();
                $deleted++;
            }

            $this->info("Đã xóa thành công {$deleted} thông báo và tất cả recipients liên quan.");
        } else {
            $this->info('Đã hủy thao tác.');
        }

        return 0;
    }
}
