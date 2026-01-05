<?php

namespace App\Console\Commands;

use App\Models\AdminLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanOldLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean {--days=90}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động xóa nhật ký cũ (mặc định 90 ngày)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);

        try {
            $deleted = AdminLog::where('created_at', '<', $cutoffDate)->delete();
            
            $this->info("✅ Đã xóa {$deleted} bản ghi nhật ký cũ hơn {$days} ngày");
            return 0;
        } catch (\Exception $e) {
            $this->error('Lỗi khi xóa nhật ký: ' . $e->getMessage());
            return 1;
        }
    }
}

