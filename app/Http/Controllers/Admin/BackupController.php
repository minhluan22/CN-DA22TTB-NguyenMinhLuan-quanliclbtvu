<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    /**
     * Hiển thị trang sao lưu dữ liệu
     */
    public function index()
    {
        $backups = $this->getBackupFiles();
        
        // Lấy cấu hình sao lưu tự động
        $autoBackupConfig = [
            'daily_enabled' => \App\Models\SystemConfig::getValue('backup_daily_enabled', false),
            'weekly_enabled' => \App\Models\SystemConfig::getValue('backup_weekly_enabled', false),
            'monthly_enabled' => \App\Models\SystemConfig::getValue('backup_monthly_enabled', false),
        ];
        
        return view('admin.backup.index', compact('backups', 'autoBackupConfig'));
    }

    /**
     * Tạo backup database
     */
    public function createBackup(Request $request)
    {
        try {
            $driver = config('database.default');
            
            if ($driver === 'sqlite') {
                // Backup SQLite
                $databasePath = database_path('database.sqlite');
                if (!File::exists($databasePath)) {
                    return redirect()->back()->with('error', 'Không tìm thấy file database!');
                }

                $backupDir = storage_path('app/backups');
                if (!File::exists($backupDir)) {
                    File::makeDirectory($backupDir, 0755, true);
                }

                $timestamp = now()->format('Y-m-d_H-i-s');
                $filename = "backup_{$timestamp}.sqlite";
                $backupPath = $backupDir . '/' . $filename;

                File::copy($databasePath, $backupPath);

                // Ghi log
                AdminLog::createLog(
                    auth()->id(),
                    'backup',
                    'SystemConfig',
                    null,
                    "Tạo backup database: {$filename}",
                    null,
                    ['filename' => $filename, 'size' => File::size($backupPath)]
                );

                return redirect()->back()->with('success', "Sao lưu thành công: {$filename}");
            } else {
                // Backup MySQL/PostgreSQL sử dụng Artisan command
                $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
                Artisan::call('db:backup', ['--filename' => $filename]);
                
                AdminLog::createLog(
                    auth()->id(),
                    'backup',
                    'SystemConfig',
                    null,
                    "Tạo backup database: {$filename}",
                    null,
                    ['filename' => $filename]
                );

                return redirect()->back()->with('success', "Sao lưu thành công: {$filename}");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi sao lưu: ' . $e->getMessage());
        }
    }

    /**
     * Tải xuống file backup
     */
    public function download($filename)
    {
        $backupDir = storage_path('app/backups');
        $filePath = $backupDir . '/' . $filename;

        if (!File::exists($filePath)) {
            return redirect()->back()->with('error', 'File backup không tồn tại!');
        }

        // Ghi log
        AdminLog::createLog(
            auth()->id(),
            'backup',
            'SystemConfig',
            null,
            "Tải xuống backup: {$filename}",
            null,
            ['filename' => $filename]
        );

        return response()->download($filePath);
    }

    /**
     * Xóa file backup
     */
    public function delete(Request $request, $filename)
    {
        try {
            $backupDir = storage_path('app/backups');
            $filePath = $backupDir . '/' . $filename;

            if (!File::exists($filePath)) {
                return redirect()->back()->with('error', 'File backup không tồn tại!');
            }

            File::delete($filePath);

            // Ghi log
            AdminLog::createLog(
                auth()->id(),
                'delete',
                'SystemConfig',
                null,
                "Xóa backup: {$filename}",
                ['filename' => $filename],
                null
            );

            return redirect()->back()->with('success', 'Xóa backup thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }

    /**
     * Khôi phục database từ backup
     */
    public function restore(Request $request, $filename)
    {
        try {
            $backupDir = storage_path('app/backups');
            $filePath = $backupDir . '/' . $filename;

            if (!File::exists($filePath)) {
                return redirect()->back()->with('error', 'File backup không tồn tại!');
            }

            $driver = config('database.default');
            
            if ($driver === 'sqlite') {
                $databasePath = database_path('database.sqlite');
                
                // Tạo backup trước khi restore
                $preRestoreBackup = 'pre_restore_' . now()->format('Y-m-d_H-i-s') . '.sqlite';
                if (File::exists($databasePath)) {
                    File::copy($databasePath, $backupDir . '/' . $preRestoreBackup);
                }

                // Restore
                File::copy($filePath, $databasePath);

                // Ghi log
                AdminLog::createLog(
                    auth()->id(),
                    'restore',
                    'SystemConfig',
                    null,
                    "Khôi phục database từ: {$filename}",
                    null,
                    ['filename' => $filename, 'pre_backup' => $preRestoreBackup]
                );

                return redirect()->back()->with('success', "Khôi phục thành công từ: {$filename}");
            } else {
                return redirect()->back()->with('error', 'Chức năng restore chỉ hỗ trợ SQLite hiện tại!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi khôi phục: ' . $e->getMessage());
        }
    }

    /**
     * Lấy danh sách file backup
     */
    private function getBackupFiles()
    {
        $backupDir = storage_path('app/backups');
        
        if (!File::exists($backupDir)) {
            return [];
        }

        $files = File::files($backupDir);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $file->getSize(),
                'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                'size_human' => $this->formatBytes($file->getSize()),
            ];
        }

        // Sắp xếp theo thời gian tạo (mới nhất trước)
        usort($backups, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $backups;
    }

    /**
     * Format bytes thành human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Cập nhật cấu hình sao lưu tự động
     */
    public function updateAutoBackup(Request $request)
    {
        $request->validate([
            'daily_enabled' => 'boolean',
            'weekly_enabled' => 'boolean',
            'monthly_enabled' => 'boolean',
        ]);

        try {
            \App\Models\SystemConfig::setValue('backup_daily_enabled', $request->has('daily_enabled') ? '1' : '0', 'backup', 'boolean');
            \App\Models\SystemConfig::setValue('backup_weekly_enabled', $request->has('weekly_enabled') ? '1' : '0', 'backup', 'boolean');
            \App\Models\SystemConfig::setValue('backup_monthly_enabled', $request->has('monthly_enabled') ? '1' : '0', 'backup', 'boolean');

            // Ghi log
            AdminLog::createLog(
                auth()->id(),
                'update',
                'SystemConfig',
                null,
                'Cập nhật cấu hình sao lưu tự động',
                null,
                [
                    'daily_enabled' => $request->has('daily_enabled'),
                    'weekly_enabled' => $request->has('weekly_enabled'),
                    'monthly_enabled' => $request->has('monthly_enabled'),
                ]
            );

            return redirect()->back()->with('success', 'Cập nhật cấu hình sao lưu tự động thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }
}

