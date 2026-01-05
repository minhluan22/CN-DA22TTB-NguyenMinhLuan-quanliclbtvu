<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Violation;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ViolationController extends BaseAdminController
{
    /**
     * Danh sách vi phạm
     */
    public function index(Request $request)
    {
        $query = Violation::with(['user', 'club', 'regulation', 'recorder', 'processor']);

        // Áp dụng filters chung
        $query = $this->applyFilters($query, $request, [
            'club_id' => ['type' => 'exact', 'column' => 'club_id'],
            'user_id' => ['type' => 'exact', 'column' => 'user_id'],
            'severity' => ['type' => 'exact', 'column' => 'severity'],
            'status' => ['type' => 'exact', 'column' => 'status'],
        ]);

        // Áp dụng search với relation
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('student_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('club', function($clubQuery) use ($search) {
                      $clubQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // Pagination
        $violations = $this->paginateWithQueryString($query, 10, 'violation_date', 'desc');

        // Thống kê
        $stats = [
            'total' => Violation::count(),
            'pending' => Violation::where('status', 'pending')->count(),
            'processed' => Violation::where('status', 'processed')->count(),
            'monitoring' => Violation::where('status', 'monitoring')->count(),
        ];

        $clubs = $this->getActiveClubs();

        return view('admin.violations.index', compact('violations', 'stats', 'clubs'));
    }

    /**
     * Xem chi tiết vi phạm
     */
    public function show($id)
    {
        $violation = Violation::with([
            'user',
            'club',
            'regulation',
            'recorder',
            'processor'
        ])->findOrFail($id);

        return view('admin.violations.show', compact('violation'));
    }

    /**
     * Danh sách vi phạm cần xử lý kỷ luật
     */
    public function handleList(Request $request)
    {
        $query = Violation::with(['user', 'club', 'regulation', 'recorder'])
            ->whereIn('status', ['pending', 'monitoring']);

        // Lọc theo CLB
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        // Lọc theo mức độ
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('student_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('club', function($clubQuery) use ($search) {
                      $clubQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        $violations = $query->orderBy('violation_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $clubs = Club::where('status', 'active')->orderBy('name')->get();

        return view('admin.violations.handle-list', compact('violations', 'clubs'));
    }

    /**
     * Xử lý kỷ luật cho một vi phạm cụ thể
     */
    public function handle($id)
    {
        $violation = Violation::with(['user', 'club', 'regulation'])->findOrFail($id);
        
        if ($violation->status == 'processed') {
            return redirect()->route('admin.violations.show', $id)
                ->with('error', 'Vi phạm này đã được xử lý.');
        }

        return view('admin.violations.handle', compact('violation'));
    }

    /**
     * Xử lý kỷ luật (POST)
     */
    public function processDiscipline(Request $request, $id)
    {
        $violation = Violation::findOrFail($id);

        if ($violation->status == 'processed') {
            return back()->with('error', 'Vi phạm này đã được xử lý.');
        }

        $request->validate([
            'discipline_type' => 'required|in:warning,reprimand,suspension,expulsion,ban',
            'discipline_reason' => 'required|string|max:1000',
            'discipline_period_start' => 'nullable|date',
            'discipline_period_end' => 'nullable|date|after_or_equal:discipline_period_start',
            'status' => 'required|in:processed,monitoring',
        ]);

        $violation->update([
            'discipline_type' => $request->discipline_type,
            'discipline_reason' => $request->discipline_reason,
            'discipline_period_start' => $request->discipline_period_start,
            'discipline_period_end' => $request->discipline_period_end,
            'status' => $request->status,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        // TODO: Gửi thông báo cho sinh viên và chủ nhiệm CLB

        return redirect()->route('admin.violations.show', $id)
            ->with('success', 'Xử lý kỷ luật thành công!');
    }

    /**
     * Lịch sử kỷ luật - Chỉ hiển thị các vi phạm đã được xử lý
     */
    public function history(Request $request)
    {
        $query = Violation::with(['user', 'club', 'regulation', 'recorder', 'processor'])
            ->where('status', 'processed')
            ->whereNotNull('processed_by')
            ->whereNotNull('processed_at');

        // Lọc theo CLB
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        // Lọc theo sinh viên
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo hình thức kỷ luật
        if ($request->filled('discipline_type')) {
            $query->where('discipline_type', $request->discipline_type);
        }

        // Lọc theo người xử lý
        if ($request->filled('processed_by')) {
            $query->where('processed_by', $request->processed_by);
        }

        // Lọc theo thời gian
        if ($request->filled('start_date')) {
            $query->whereDate('processed_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('processed_at', '<=', $request->end_date);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('discipline_reason', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('student_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('club', function($clubQuery) use ($search) {
                      $clubQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('processor', function($processorQuery) use ($search) {
                      $processorQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $violations = $query->orderBy('processed_at', 'desc')
            ->orderBy('violation_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Thống kê
        $stats = [
            'total' => Violation::where('status', 'processed')->count(),
            'by_type' => Violation::where('status', 'processed')
                ->whereNotNull('discipline_type')
                ->select('discipline_type', DB::raw('count(*) as count'))
                ->groupBy('discipline_type')
                ->get(),
        ];

        $clubs = Club::where('status', 'active')->orderBy('name')->get();
        
        // Lấy danh sách admin đã xử lý
        $processors = DB::table('users')
            ->join('violations', 'users.id', '=', 'violations.processed_by')
            ->where('violations.status', 'processed')
            ->select('users.id', 'users.name')
            ->distinct()
            ->orderBy('users.name')
            ->get();

        return view('admin.violations.history', compact('violations', 'stats', 'clubs', 'processors'));
    }

    /**
     * Xuất báo cáo danh sách vi phạm
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        // Lấy dữ liệu với cùng filter như index
        $query = Violation::with(['user', 'club', 'regulation', 'recorder', 'processor']);

        // Lọc theo CLB
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        // Lọc theo sinh viên
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo mức độ
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('student_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('club', function($clubQuery) use ($search) {
                      $clubQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        $violations = $query->orderBy('violation_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'excel') {
            return $this->exportViolationsToExcel($violations);
        } else {
            return $this->exportViolationsToPDF($violations);
        }
    }

    /**
     * Xuất báo cáo lịch sử kỷ luật
     */
    public function exportHistory(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        // Lấy dữ liệu với cùng filter như history
        $query = Violation::with(['user', 'club', 'regulation', 'recorder', 'processor'])
            ->where('status', 'processed')
            ->whereNotNull('processed_by')
            ->whereNotNull('processed_at');

        // Lọc theo CLB
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        // Lọc theo sinh viên
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo hình thức kỷ luật
        if ($request->filled('discipline_type')) {
            $query->where('discipline_type', $request->discipline_type);
        }

        // Lọc theo người xử lý
        if ($request->filled('processed_by')) {
            $query->where('processed_by', $request->processed_by);
        }

        // Lọc theo thời gian
        if ($request->filled('start_date')) {
            $query->whereDate('processed_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('processed_at', '<=', $request->end_date);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('discipline_reason', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('student_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('club', function($clubQuery) use ($search) {
                      $clubQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('processor', function($processorQuery) use ($search) {
                      $processorQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $violations = $query->orderBy('processed_at', 'desc')
            ->orderBy('violation_date', 'desc')
            ->get();

        // Thống kê
        $stats = [
            'total' => $violations->count(),
            'by_type' => $violations->groupBy('discipline_type')->map->count(),
            'by_club' => $violations->groupBy('club_id')->map->count(),
        ];

        if ($format === 'excel') {
            return $this->exportHistoryToExcel($violations, $stats);
        } else {
            return $this->exportHistoryToPDF($violations, $stats);
        }
    }

    /**
     * Xuất Excel - Danh sách vi phạm
     */
    private function exportViolationsToExcel($violations)
    {
        $filename = 'danh_sach_vi_pham_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($violations) {
            $file = fopen('php://output', 'w');
            
            // BOM cho UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'STT',
                'Sinh viên',
                'MSSV',
                'CLB',
                'Mã CLB',
                'Nội quy vi phạm',
                'Mã nội quy',
                'Mô tả vi phạm',
                'Mức độ',
                'Thời gian xảy ra',
                'Người ghi nhận',
                'Trạng thái',
                'Hình thức kỷ luật',
                'Lý do kỷ luật',
                'Thời hạn kỷ luật',
                'Người xử lý',
                'Thời gian xử lý'
            ]);

            // Data
            $stt = 1;
            foreach ($violations as $violation) {
                $severityNames = ['light' => 'Nhẹ', 'medium' => 'Trung bình', 'serious' => 'Nghiêm trọng'];
                $statusNames = ['pending' => 'Chưa xử lý', 'processed' => 'Đã xử lý', 'monitoring' => 'Đang theo dõi'];
                $disciplineNames = [
                    'warning' => 'Cảnh cáo',
                    'reprimand' => 'Khiển trách',
                    'suspension' => 'Đình chỉ',
                    'expulsion' => 'Buộc rời CLB',
                    'ban' => 'Cấm tham gia'
                ];

                $period = '';
                if ($violation->discipline_period_start && $violation->discipline_period_end) {
                    $period = \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y') . 
                              ' - ' . \Carbon\Carbon::parse($violation->discipline_period_end)->format('d/m/Y');
                } elseif ($violation->discipline_period_start) {
                    $period = 'Từ ' . \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y');
                }

                fputcsv($file, [
                    $stt++,
                    $violation->user->name ?? 'N/A',
                    $violation->user->student_code ?? 'N/A',
                    $violation->club->name ?? 'N/A',
                    $violation->club->code ?? 'N/A',
                    $violation->regulation->title ?? 'N/A',
                    $violation->regulation->code ?? 'N/A',
                    $violation->description,
                    $severityNames[$violation->severity] ?? $violation->severity,
                    \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y H:i'),
                    $violation->recorder->name ?? 'N/A',
                    $statusNames[$violation->status] ?? $violation->status,
                    $violation->discipline_type ? ($disciplineNames[$violation->discipline_type] ?? $violation->discipline_type) : '',
                    $violation->discipline_reason ?? '',
                    $period,
                    $violation->processor->name ?? 'N/A',
                    $violation->processed_at ? \Carbon\Carbon::parse($violation->processed_at)->format('d/m/Y H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Xuất PDF - Danh sách vi phạm
     */
    private function exportViolationsToPDF($violations)
    {
        $filename = 'danh_sach_vi_pham_' . date('Y-m-d_His') . '.pdf';
        
        $data = [
            'title' => 'Danh sách vi phạm',
            'violations' => $violations,
            'generated_at' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('admin.violations.export-pdf', $data);
        return $pdf->download($filename);
    }

    /**
     * Xuất Excel - Lịch sử kỷ luật
     */
    private function exportHistoryToExcel($violations, $stats)
    {
        $filename = 'lich_su_ky_luat_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($violations, $stats) {
            $file = fopen('php://output', 'w');
            
            // BOM cho UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Thống kê
            fputcsv($file, ['THỐNG KÊ LỊCH SỬ KỶ LUẬT']);
            fputcsv($file, ['Tổng số vi phạm đã xử lý:', $stats['total']]);
            fputcsv($file, []);
            
            // Headers
            fputcsv($file, [
                'STT',
                'Sinh viên',
                'MSSV',
                'CLB',
                'Mã CLB',
                'Nội quy vi phạm',
                'Mã nội quy',
                'Hành vi vi phạm',
                'Hình thức kỷ luật',
                'Lý do xử lý',
                'Thời gian áp dụng',
                'Người xử lý (Admin)',
                'Thời gian xử lý'
            ]);

            // Data
            $stt = 1;
            foreach ($violations as $violation) {
                $disciplineNames = [
                    'warning' => 'Cảnh cáo',
                    'reprimand' => 'Khiển trách',
                    'suspension' => 'Đình chỉ',
                    'expulsion' => 'Buộc rời CLB',
                    'ban' => 'Cấm tham gia'
                ];

                $period = '';
                if ($violation->discipline_period_start && $violation->discipline_period_end) {
                    $period = \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y') . 
                              ' - ' . \Carbon\Carbon::parse($violation->discipline_period_end)->format('d/m/Y');
                } elseif ($violation->discipline_period_start) {
                    $period = 'Từ ' . \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y');
                } else {
                    $period = 'Không giới hạn';
                }

                fputcsv($file, [
                    $stt++,
                    $violation->user->name ?? 'N/A',
                    $violation->user->student_code ?? 'N/A',
                    $violation->club->name ?? 'N/A',
                    $violation->club->code ?? 'N/A',
                    $violation->regulation->title ?? 'N/A',
                    $violation->regulation->code ?? 'N/A',
                    $violation->description,
                    $violation->discipline_type ? ($disciplineNames[$violation->discipline_type] ?? $violation->discipline_type) : 'N/A',
                    $violation->discipline_reason ?? '',
                    $period,
                    $violation->processor->name ?? 'N/A',
                    \Carbon\Carbon::parse($violation->processed_at)->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Xuất PDF - Lịch sử kỷ luật
     */
    private function exportHistoryToPDF($violations, $stats)
    {
        $filename = 'lich_su_ky_luat_' . date('Y-m-d_His') . '.pdf';
        
        $data = [
            'title' => 'Lịch sử kỷ luật',
            'violations' => $violations,
            'stats' => $stats,
            'generated_at' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('admin.violations.export-history-pdf', $data);
        return $pdf->download($filename);
    }
}
