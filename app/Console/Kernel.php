<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Registered commands
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sao lưu tự động hàng ngày (2:00 AM)
        $schedule->command('backup:auto --type=daily')
            ->dailyAt('02:00')
            ->withoutOverlapping();

        // Sao lưu tự động hàng tuần (Chủ nhật 3:00 AM)
        $schedule->command('backup:auto --type=weekly')
            ->weeklyOn(0, '03:00')
            ->withoutOverlapping();

        // Sao lưu tự động hàng tháng (Ngày 1, 4:00 AM)
        $schedule->command('backup:auto --type=monthly')
            ->monthlyOn(1, '04:00')
            ->withoutOverlapping();

        // Tự động xóa nhật ký cũ (hàng tuần, Chủ nhật 5:00 AM)
        $schedule->command('logs:clean --days=90')
            ->weeklyOn(0, '05:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
