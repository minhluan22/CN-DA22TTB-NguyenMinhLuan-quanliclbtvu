<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminLogController extends Controller
{
    /**
     * Hiển thị danh sách nhật ký admin
     */
    public function index(Request $request)
    {
        $query = AdminLog::with('admin')
            ->orderBy('created_at', 'desc');

        // Filter theo action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter theo model_type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter theo admin
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Filter theo từ khóa (tìm trong description)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('description', 'like', "%{$keyword}%");
        }

        // Filter theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Lấy danh sách admin cho filter
        $admins = \App\Models\User::whereHas('role', function($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })->get();

        // Danh sách actions
        $actions = [
            'create' => 'Thêm mới',
            'update' => 'Cập nhật',
            'delete' => 'Xóa',
            'approve' => 'Phê duyệt',
            'reject' => 'Từ chối',
            'enable' => 'Kích hoạt',
            'disable' => 'Vô hiệu hóa',
            'backup' => 'Sao lưu',
            'restore' => 'Khôi phục',
        ];

        // Danh sách model types
        $modelTypes = [
            'User' => 'Tài khoản',
            'Club' => 'Câu lạc bộ',
            'Activity' => 'Hoạt động',
            'Event' => 'Sự kiện',
            'Regulation' => 'Nội quy',
            'Violation' => 'Vi phạm',
            'SystemConfig' => 'Cấu hình hệ thống',
            'Notification' => 'Thông báo',
        ];

        return view('admin.admin-log.index', compact('logs', 'admins', 'actions', 'modelTypes'));
    }

    /**
     * Xem chi tiết log
     */
    public function show($id)
    {
        $log = AdminLog::with('admin')->findOrFail($id);
        return view('admin.admin-log.show', compact('log'));
    }

    /**
     * Xuất nhật ký (PDF hoặc Excel)
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel'); // excel hoặc pdf

        $query = AdminLog::with('admin')
            ->orderBy('created_at', 'desc');

        // Áp dụng các filter giống như index
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }
        if ($request->filled('keyword')) {
            $query->where('description', 'like', "%{$request->keyword}%");
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        if ($format === 'pdf') {
            return $this->exportToPDF($logs);
        } else {
            return $this->exportToExcel($logs);
        }
    }

    /**
     * Xuất Excel
     */
    private function exportToExcel($logs)
    {
        $filename = 'nhat_ky_admin_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // BOM cho UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'STT',
                'Thời gian',
                'Admin',
                'Email Admin',
                'Hành động',
                'Đối tượng',
                'ID Đối tượng',
                'Mô tả',
                'IP Address',
                'User Agent',
            ]);

            foreach ($logs as $index => $log) {
                fputcsv($file, [
                    $index + 1,
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->admin->name ?? 'N/A',
                    $log->admin->email ?? 'N/A',
                    $log->action_name,
                    $log->model_name,
                    $log->model_id ?? '—',
                    $log->description ?? '—',
                    $log->ip_address ?? '—',
                    $log->user_agent ?? '—',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Xuất PDF
     */
    private function exportToPDF($logs)
    {
        $filename = 'nhat_ky_admin_' . date('Y-m-d_His') . '.pdf';
        
        $data = [
            'title' => 'Nhật ký Admin',
            'logs' => $logs,
            'generated_at' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('admin.admin-log.export-pdf', $data);
        return $pdf->download($filename);
    }
}

