<?php

namespace App\Console\Commands;

use App\Models\AdminLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AutoBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:auto {--type=daily}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động sao lưu database theo lịch (daily, weekly, monthly)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type'); // daily, weekly, monthly
        
        try {
            $driver = config('database.default');
            
            if ($driver === 'sqlite') {
                $databasePath = database_path('database.sqlite');
                if (!File::exists($databasePath)) {
                    $this->error('Không tìm thấy file database!');
                    return 1;
                }

                $backupDir = storage_path('app/backups');
                if (!File::exists($backupDir)) {
                    File::makeDirectory($backupDir, 0755, true);
                }

                $timestamp = now()->format('Y-m-d_H-i-s');
                $filename = "auto_backup_{$type}_{$timestamp}.sqlite";
                $backupPath = $backupDir . '/' . $filename;

                File::copy($databasePath, $backupPath);

                // Ghi log (sử dụng system user)
                AdminLog::create([
                    'admin_id' => 1, // System user
                    'action' => 'backup',
                    'model_type' => 'SystemConfig',
                    'model_id' => null,
                    'description' => "Tự động sao lưu ({$type}): {$filename}",
                    'old_data' => null,
                    'new_data' => ['filename' => $filename, 'size' => File::size($backupPath), 'type' => $type],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'System Auto Backup',
                ]);

                $this->info("✅ Sao lưu tự động thành công: {$filename}");
                return 0;
            } else {
                $this->error('Chức năng tự động backup chỉ hỗ trợ SQLite hiện tại!');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Lỗi khi sao lưu: ' . $e->getMessage());
            return 1;
        }
    }
}

