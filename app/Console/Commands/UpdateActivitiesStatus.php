<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\ActivityController;
use Illuminate\Support\Facades\DB;

class UpdateActivitiesStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật status cho tất cả hoạt động theo thời gian thực tế và sửa lại status của registrations không hợp lý';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Bắt đầu cập nhật status cho tất cả hoạt động...');

        $controller = new ActivityController();
        $updated = $controller->updateAllActivitiesStatus();

        $this->info("✅ Đã cập nhật {$updated} hoạt động.");
        
        return Command::SUCCESS;
    }
}

