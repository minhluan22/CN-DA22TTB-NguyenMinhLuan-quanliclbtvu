<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Event;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivityController extends BaseAdminController
{
    /**
     * Danh sách hoạt động - Admin xem tất cả hoạt động
     */
    public function index(Request $request)
    {
        $query = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->leftJoin('users', 'events.created_by', '=', 'users.id')
            ->leftJoin('club_members', function($join) {
                $join->on('users.id', '=', 'club_members.user_id')
                     ->on('events.club_id', '=', 'club_members.club_id');
            })
            ->select(
                'events.*',
                'clubs.name as club_name',
                'clubs.code as club_code',
                'clubs.owner_id',
                'users.name as creator_name',
                'users.student_code as creator_student_code',
                'users.id as creator_id',
                'club_members.position as creator_position'
            );

        // Áp dụng approval_status filter đặc biệt TRƯỚC (để xử lý trường hợp 'disabled')
        // Nếu approval_status = 'disabled', sẽ filter theo status = 'disabled' và bỏ qua filter status khác
        $hasDisabledFilter = $request->filled('approval_status') && $request->input('approval_status') === 'disabled';
        
        // Áp dụng filters chung với table prefix đầy đủ
        // Override status và approval_status để tránh dùng defaultFilters (có thể gây ambiguous column)
        $filterConfig = [
            'club_id' => ['type' => 'exact', 'column' => 'events.club_id'],
        ];
        
        // Nếu KHÔNG phải filter 'disabled', thêm status và approval_status với table prefix
        if (!$hasDisabledFilter) {
            if ($request->filled('status')) {
                $filterConfig['status'] = ['type' => 'exact', 'column' => 'events.status'];
            }
            if ($request->filled('approval_status')) {
                $filterConfig['approval_status'] = ['type' => 'exact', 'column' => 'events.approval_status'];
            }
        } else {
            // Nếu là filter 'disabled', override để skip filter này (tránh dùng defaultFilters không có prefix)
            // Sẽ được xử lý riêng bằng applyApprovalStatusFilter
            if ($request->filled('status')) {
                $filterConfig['status'] = ['type' => 'skip', 'column' => 'events.status'];
            }
            if ($request->filled('approval_status')) {
                $filterConfig['approval_status'] = ['type' => 'skip', 'column' => 'events.approval_status'];
            }
        }
        
        $query = $this->applyFilters($query, $request, $filterConfig);

        // Áp dụng approval_status filter đặc biệt (sẽ xử lý trường hợp 'disabled' riêng)
        $query = $this->applyApprovalStatusFilter($query, $request, 'events.status', 'events.approval_status');

        // Áp dụng date range
        $query = $this->applyDateRange($query, $request, 'events.start_at');

        // Áp dụng search
        $query = $this->applySearch($query, $request, [
            'events.title',
            'clubs.name',
            'clubs.code'
        ]);

        // Pagination
        $activities = $this->paginateWithQueryString($query, 10, 'events.start_at', 'desc');

        // Đếm số người đăng ký và tham gia, cập nhật status theo thời gian
        $now = \Carbon\Carbon::now();
        foreach ($activities as $activity) {
            // Cập nhật status của hoạt động theo thời gian thực tế
            $this->updateActivityStatusByTime($activity);
            
            // Sửa lại status của registrations không hợp lý
            $this->fixInvalidRegistrationStatuses($activity->id, $activity->status, $activity->end_at);
            
            // Đếm số người đăng ký (tất cả trừ rejected)
            $activity->registered_count = DB::table('event_registrations')
                ->where('event_id', $activity->id)
                ->where('status', '!=', 'rejected')
                ->count();
            
            // Đếm số người tham gia theo logic:
            // - Nếu hoạt động đã kết thúc: đếm những người có status = 'attended'
            // - Nếu chưa kết thúc: đếm những người có status = 'approved' (đã được duyệt)
            if ($activity->status === 'finished') {
                // Hoạt động đã kết thúc: chỉ đếm những người đã tham gia
                $activity->participant_count = DB::table('event_registrations')
                    ->where('event_id', $activity->id)
                    ->where('status', 'attended')
                    ->count();
            } else {
                // Hoạt động chưa kết thúc: đếm những người đã được duyệt
                $activity->participant_count = DB::table('event_registrations')
                    ->where('event_id', $activity->id)
                    ->where('status', 'approved')
                    ->count();
            }
        }

        // Lấy danh sách CLB cho filter
        $clubs = $this->getActiveClubs();

        return view('admin.activities.index', compact('activities', 'clubs'));
    }

    /**
     * Xem chi tiết hoạt động
     */
    public function show($id)
    {
        $activity = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->leftJoin('users', 'events.created_by', '=', 'users.id')
            ->leftJoin('club_members', function($join) {
                $join->on('users.id', '=', 'club_members.user_id')
                     ->on('events.club_id', '=', 'club_members.club_id');
            })
            ->where('events.id', $id)
            ->select(
                'events.*',
                'clubs.name as club_name',
                'clubs.code as club_code',
                'clubs.logo as club_logo',
                'clubs.owner_id',
                'users.name as creator_name',
                'users.student_code as creator_student_code',
                'users.id as creator_id',
                'club_members.position as creator_position'
            )
            ->first();

        if (!$activity) {
            return back()->with('error', 'Hoạt động không tồn tại.');
        }

        // Số người đăng ký
        $registeredCount = DB::table('event_registrations')
            ->where('event_id', $id)
            ->count();

        // Số người đã duyệt
        $approvedCount = DB::table('event_registrations')
            ->where('event_id', $id)
            ->where('status', 'approved')
            ->count();

        // Số người đã tham gia
        $attendedCount = DB::table('event_registrations')
            ->where('event_id', $id)
            ->where('status', 'attended')
            ->count();

        // Danh sách người tham gia
        $participants = DB::table('event_registrations')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('event_registrations.event_id', $id)
            ->whereIn('event_registrations.status', ['approved', 'attended'])
            ->select(
                'users.id',
                'users.name',
                'users.student_code',
                'users.email',
                'event_registrations.status',
                'event_registrations.activity_points',
                'event_registrations.created_at as registered_at'
            )
            ->orderBy('event_registrations.created_at', 'asc')
            ->get();

        return view('admin.activities.show', compact(
            'activity',
            'registeredCount',
            'approvedCount',
            'attendedCount',
            'participants'
        ));
    }

    /**
     * Danh sách hoạt động vi phạm
     */
    public function violations(Request $request)
    {
        $query = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->leftJoin('users as creators', 'events.created_by', '=', 'creators.id')
            ->leftJoin('users as recorders', 'events.violation_recorded_by', '=', 'recorders.id')
            // Chỉ hiển thị các hoạt động có dấu hiệu vi phạm
            ->where(function($q) {
                $q->whereNotNull('events.violation_notes')
                  ->orWhereNotNull('events.violation_status')
                  ->orWhere('events.status', 'disabled');
            })
            ->select(
                'events.*',
                'clubs.name as club_name',
                'clubs.code as club_code',
                'creators.name as creator_name',
                'recorders.name as recorder_name'
            );

        // Áp dụng filters chung
        $query = $this->applyFilters($query, $request, [
            'club_id' => ['type' => 'exact', 'column' => 'events.club_id'],
            'severity' => ['type' => 'exact', 'column' => 'events.violation_severity'],
            'violation_status' => ['type' => 'exact', 'column' => 'events.violation_status'],
        ]);

        // Áp dụng search
        $query = $this->applySearch($query, $request, [
            'events.title',
            'clubs.name',
            'events.violation_type'
        ]);

        // Pagination với multiple order by
        $violations = $query->orderBy('events.violation_detected_at', 'desc')
            ->orderBy('events.updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Lấy danh sách CLB cho filter
        $clubs = $this->getActiveClubs();

        return view('admin.activities.violations', compact('violations', 'clubs'));
    }

    /**
     * Hiển thị form vô hiệu hóa hoạt động
     */
    public function showDisableForm(Request $request, $id)
    {
        $activity = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->leftJoin('users', 'events.created_by', '=', 'users.id')
            ->select(
                'events.*',
                'clubs.name as club_name',
                'clubs.code as club_code',
                'users.name as creator_name',
                'users.student_code as creator_student_code'
            )
            ->where('events.id', $id)
            ->first();

        if (!$activity) {
            abort(404);
        }

        // 🔑 NGUYÊN TẮC VÀNG: Chưa xử lý và Đang xử lý = PHẢI CÓ HÀNH ĐỘNG
        // Logic: Chỉ cho phép vô hiệu hóa khi:
        // 1. Chưa bị vô hiệu hóa (status != 'disabled')
        // 2. Trạng thái xử lý chưa phải là "Đã xử lý" (violation_status !== 'processed')
        //    Bao gồm: null, 'pending', 'processing' → Cho phép
        // ✅ Chưa xử lý (pending/null) → Cho phép
        // ✅ Đang xử lý (processing) → Cho phép
        // ❌ Đã xử lý (processed) → Không cho phép
        
        // Lấy query string để quay lại đúng vị trí
        $queryString = $request->getQueryString();
        
        if ($activity->status == 'disabled') {
            $redirectUrl = route('admin.activities.violations');
            if ($queryString) {
                $redirectUrl .= '?' . $queryString;
            }
            return redirect($redirectUrl)
                ->with('error', 'Hoạt động này đã bị vô hiệu hóa.');
        }

        // Kiểm tra: Không cho phép nếu đã được xử lý
        if ($activity->violation_status === 'processed') {
            $redirectUrl = route('admin.activities.violations');
            if ($queryString) {
                $redirectUrl .= '?' . $queryString;
            }
            return redirect($redirectUrl)
                ->with('error', 'Không thể vô hiệu hóa hoạt động đã được xử lý.');
        }

        // Cho phép nếu: null, 'pending', 'processing'
        $backUrl = route('admin.activities.violations');
        if ($queryString) {
            $backUrl .= '?' . $queryString;
        }

        return view('admin.activities.disable', compact('activity', 'backUrl'));
    }

    /**
     * Vô hiệu hóa hoạt động và đánh dấu vi phạm
     */
    public function disable(Request $request, $id)
    {
        $request->validate([
            'violation_notes' => 'required|string|max:1000',
            'violation_type' => 'required|string|max:255',
            'violation_severity' => 'required|in:light,medium,serious',
        ]);

        $event = Event::findOrFail($id);

        // 🔑 NGUYÊN TẮC VÀNG: Chưa xử lý và Đang xử lý = PHẢI CÓ HÀNH ĐỘNG
        // Logic: Kiểm tra điều kiện vô hiệu hóa
        // ✅ Chưa xử lý (pending/null) → Cho phép
        // ✅ Đang xử lý (processing) → Cho phép
        // ❌ Đã xử lý (processed) → Không cho phép
        
        // Kiểm tra: Không cho phép vô hiệu hóa nếu đã bị vô hiệu hóa
        if ($event->status == 'disabled') {
            return redirect()->route('admin.activities.violations')
                ->with('error', 'Hoạt động này đã bị vô hiệu hóa.');
        }

        // Kiểm tra: Không cho phép vô hiệu hóa nếu đã được xử lý
        if ($event->violation_status === 'processed') {
            return redirect()->route('admin.activities.violations')
                ->with('error', 'Không thể vô hiệu hóa hoạt động đã được xử lý.');
        }

        // Cho phép nếu: null, 'pending', 'processing'

        // Cập nhật trạng thái và thông tin vi phạm
        $event->update([
            'status' => 'disabled',
            'violation_notes' => $request->violation_notes,
            'violation_type' => $request->violation_type,
            'violation_severity' => $request->violation_severity,
            'violation_status' => 'pending', // Mặc định là chưa xử lý
            'violation_detected_at' => now(),
            'violation_recorded_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        // Ghi log (nếu có bảng activity_logs)
        if (Schema::hasTable('activity_logs')) {
            DB::table('activity_logs')->insert([
                'event_id' => $id,
                'admin_id' => Auth::id(),
                'action' => 'mark_violation',
                'notes' => $request->violation_notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Redirect về trang violations với query string để quay lại đúng vị trí
        $queryString = $request->input('back_query');
        $redirectUrl = route('admin.activities.violations');
        if ($queryString) {
            $redirectUrl .= '?' . $queryString;
        }
        return redirect($redirectUrl)
            ->with('success', 'Đã đánh dấu vi phạm và vô hiệu hóa hoạt động thành công.');
    }

    /**
     * Hiển thị form cập nhật xử lý vi phạm
     */
    public function showUpdateViolationForm(Request $request, $id)
    {
        $violation = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->leftJoin('users', 'events.created_by', '=', 'users.id')
            ->select(
                'events.*',
                'clubs.name as club_name',
                'clubs.code as club_code',
                'users.name as creator_name',
                'users.student_code as creator_student_code'
            )
            ->where('events.id', $id)
            ->first();

        if (!$violation) {
            abort(404);
        }
        
        // Lấy query string để quay lại đúng vị trí
        $queryString = $request->getQueryString();
        $backUrl = route('admin.activities.violations');
        if ($queryString) {
            $backUrl .= '?' . $queryString;
        }

        return view('admin.activities.update-violation', compact('violation', 'backUrl'));
    }

    /**
     * Cập nhật xử lý vi phạm
     */
    public function updateViolation(Request $request, $id)
    {
        $request->validate([
            'violation_status' => 'required|in:pending,processing,processed',
            'violation_severity' => 'nullable|in:light,medium,serious',
            'violation_notes' => 'nullable|string|max:1000',
        ]);

        $event = Event::findOrFail($id);

        $updateData = [
            'violation_status' => $request->violation_status,
        ];

        if ($request->filled('violation_severity')) {
            $updateData['violation_severity'] = $request->violation_severity;
        }

        if ($request->filled('violation_notes')) {
            $updateData['violation_notes'] = $event->violation_notes . "\n\n[Cập nhật " . now()->format('d/m/Y H:i') . "]: " . $request->violation_notes;
        }

        $event->update($updateData);

        // Ghi log
        if (Schema::hasTable('activity_logs')) {
            DB::table('activity_logs')->insert([
                'event_id' => $id,
                'admin_id' => Auth::id(),
                'action' => 'update_violation',
                'notes' => 'Cập nhật trạng thái xử lý: ' . $request->violation_status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Redirect về trang violations với query string để quay lại đúng vị trí
        $queryString = $request->input('back_query');
        $redirectUrl = route('admin.activities.violations');
        if ($queryString) {
            $redirectUrl .= '?' . $queryString;
        }
        return redirect($redirectUrl)->with('success', 'Đã cập nhật xử lý vi phạm thành công.');
    }

    /**
     * Xóa hoạt động (soft delete)
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        // Ghi log trước khi xóa (nếu có bảng activity_logs)
        if (Schema::hasTable('activity_logs')) {
            DB::table('activity_logs')->insert([
                'event_id' => $id,
                'admin_id' => Auth::id(),
                'action' => 'delete',
                'notes' => 'Hoạt động bị xóa bởi Admin: ' . Auth::user()->name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Soft delete
        $event->update([
            'status' => 'deleted',
            'deleted_at' => now(),
            'deleted_by' => Auth::id(),
        ]);

        return back()->with('success', 'Đã xóa hoạt động thành công.');
    }

    /**
     * Thống kê theo CLB
     */
    public function statisticsByClub(Request $request)
    {
        $query = Club::where('status', 'active');

        // Lọc theo CLB nếu có
        if ($request->filled('club_id')) {
            $query->where('id', $request->club_id);
        }

        $clubs = $query->orderBy('name')->get();

        $statistics = [];
        foreach ($clubs as $club) {
            // Tổng hoạt động: Tất cả hoạt động của CLB (bao gồm cả disabled)
            $totalEvents = DB::table('events')
                ->where('club_id', $club->id)
                ->count();

            // Các trạng thái hoạt động (chỉ tính các hoạt động đã duyệt)
            $ongoingEvents = DB::table('events')
                ->where('club_id', $club->id)
                ->where('approval_status', 'approved')
                ->where('status', 'ongoing')
                ->count();

            $finishedEvents = DB::table('events')
                ->where('club_id', $club->id)
                ->where('approval_status', 'approved')
                ->where('status', 'finished')
                ->count();

            $cancelledEvents = DB::table('events')
                ->where('club_id', $club->id)
                ->where('approval_status', 'approved')
                ->where('status', 'cancelled')
                ->count();

            // Bị vô hiệu hóa: Tất cả hoạt động có status = 'disabled'
            $disabledEvents = DB::table('events')
                ->where('club_id', $club->id)
                ->where('status', 'disabled')
                ->count();

            // Tổng lượt tham gia: Chỉ tính các hoạt động đã duyệt
            $totalParticipations = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->where('events.club_id', $club->id)
                ->where('events.approval_status', 'approved')
                ->whereIn('event_registrations.status', ['approved', 'attended'])
                ->count();

            // Số sinh viên tham gia (unique): Chỉ tính các hoạt động đã duyệt
            $totalUniqueParticipants = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->where('events.club_id', $club->id)
                ->where('events.approval_status', 'approved')
                ->whereIn('event_registrations.status', ['approved', 'attended'])
                ->distinct('event_registrations.user_id')
                ->count('event_registrations.user_id');

            $statistics[] = [
                'club' => $club,
                'total_events' => $totalEvents,
                'ongoing_events' => $ongoingEvents,
                'finished_events' => $finishedEvents,
                'cancelled_events' => $cancelledEvents,
                'disabled_events' => $disabledEvents,
                'total_participations' => $totalParticipations,
                'total_unique_participants' => $totalUniqueParticipants,
            ];
        }

        return view('admin.activities.statistics-by-club', compact('statistics', 'clubs'));
    }

    /**
     * Thống kê theo thời gian
     */
    public function statisticsByTime(Request $request)
    {
        // Lấy khoảng thời gian (mặc định: 12 tháng gần nhất)
        $startDate = $request->input('start_date', now()->subMonths(12)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Thống kê theo tháng
        // Tổng hoạt động: Tất cả hoạt động (bao gồm cả disabled)
        // Các trạng thái khác: Chỉ tính hoạt động đã duyệt (approval_status = 'approved')
        $monthlyQuery = DB::table('events')
            ->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
        
        // Filter theo tháng và năm nếu có
        if ($request->filled('filter_month')) {
            $filterMonth = $request->input('filter_month');
            $filterYear = $request->input('filter_year', date('Y'));
            $monthlyQuery->whereRaw('MONTH(start_at) = ? AND YEAR(start_at) = ?', [$filterMonth, $filterYear]);
        } elseif ($request->filled('filter_year')) {
            $filterYear = $request->input('filter_year');
            $monthlyQuery->whereRaw('YEAR(start_at) = ?', [$filterYear]);
        }
        
        $monthlyStats = $monthlyQuery
            ->select(
                DB::raw('DATE_FORMAT(start_at, "%Y-%m") as month'),
                // Tổng hoạt động: Tất cả
                DB::raw('COUNT(*) as event_count'),
                // Đang diễn ra: Chỉ tính approved
                DB::raw('SUM(CASE WHEN approval_status = "approved" AND status = "ongoing" THEN 1 ELSE 0 END) as ongoing_count'),
                // Đã kết thúc: Chỉ tính approved
                DB::raw('SUM(CASE WHEN approval_status = "approved" AND status = "finished" THEN 1 ELSE 0 END) as finished_count'),
                // Đã hủy: Chỉ tính approved
                DB::raw('SUM(CASE WHEN approval_status = "approved" AND status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count'),
                // Bị vô hiệu hóa: Tất cả có status = 'disabled'
                DB::raw('SUM(CASE WHEN status = "disabled" THEN 1 ELSE 0 END) as disabled_count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Thống kê theo học kỳ (nếu có)
        // Giả sử học kỳ 1: 9-12, học kỳ 2: 1-5
        $semesterStats = [];
        foreach ($monthlyStats as $stat) {
            $month = (int)substr($stat->month, 5, 2);
            $year = substr($stat->month, 0, 4);
            
            if ($month >= 9 && $month <= 12) {
                $semester = "HK1/{$year}";
            } elseif ($month >= 1 && $month <= 5) {
                $semester = "HK2/{$year}";
            } else {
                $semester = "Hè/{$year}";
            }

            // Filter theo học kỳ và năm nếu có
            if ($request->filled('filter_semester')) {
                $filterSemester = $request->input('filter_semester');
                $filterSemesterYear = $request->input('filter_semester_year', date('Y'));
                
                $shouldInclude = false;
                if ($filterSemester === 'HK1' && $semester === "HK1/{$filterSemesterYear}") {
                    $shouldInclude = true;
                } elseif ($filterSemester === 'HK2' && $semester === "HK2/{$filterSemesterYear}") {
                    $shouldInclude = true;
                } elseif ($filterSemester === 'He' && $semester === "Hè/{$filterSemesterYear}") {
                    $shouldInclude = true;
                }
                
                if (!$shouldInclude) {
                    continue;
                }
            } elseif ($request->filled('filter_semester_year')) {
                $filterSemesterYear = $request->input('filter_semester_year');
                if (substr($semester, -4) !== $filterSemesterYear) {
                    continue;
                }
            }

            if (!isset($semesterStats[$semester])) {
                $semesterStats[$semester] = [
                    'semester' => $semester,
                    'event_count' => 0,
                    'ongoing_count' => 0,
                    'finished_count' => 0,
                    'cancelled_count' => 0,
                    'disabled_count' => 0,
                ];
            }

            $semesterStats[$semester]['event_count'] += $stat->event_count;
            $semesterStats[$semester]['ongoing_count'] += $stat->ongoing_count;
            $semesterStats[$semester]['finished_count'] += $stat->finished_count;
            $semesterStats[$semester]['cancelled_count'] += $stat->cancelled_count;
            $semesterStats[$semester]['disabled_count'] += $stat->disabled_count ?? 0;
        }

        // Thống kê theo năm học: Tất cả hoạt động
        $yearlyQuery = DB::table('events')
            ->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
        
        // Filter theo năm nếu có
        if ($request->filled('filter_year_only')) {
            $filterYearOnly = $request->input('filter_year_only');
            $yearlyQuery->whereRaw('YEAR(start_at) = ?', [$filterYearOnly]);
        }
        
        $yearlyStats = $yearlyQuery
            ->select(
                DB::raw('YEAR(start_at) as year'),
                DB::raw('COUNT(*) as event_count')
            )
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();

        // Tổng quan: Tất cả hoạt động (bao gồm cả disabled)
        $totalEvents = DB::table('events')
            ->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59'])
            ->count();

        $totalParticipations = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.approval_status', 'approved')
            ->whereIn('event_registrations.status', ['approved', 'attended'])
            ->whereBetween('event_registrations.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->count();

        return view('admin.activities.statistics-by-time', compact(
            'monthlyStats',
            'semesterStats',
            'yearlyStats',
            'totalEvents',
            'totalParticipations',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Xuất báo cáo
     */
    public function exportReport(Request $request)
    {
        return view('admin.activities.export');
    }

    /**
     * Generate export report
     */
    public function generateExportReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:activities,violations,statistics',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:excel,pdf',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;
        $reportType = $request->report_type;

        $data = [];
        $filename = '';

        switch ($reportType) {
            case 'activities':
                $data = DB::table('events')
                    ->join('clubs', 'events.club_id', '=', 'clubs.id')
                    ->leftJoin('users', 'events.created_by', '=', 'users.id')
                    ->where('events.approval_status', 'approved')
                    ->whereBetween('events.start_at', [$startDate, $endDate . ' 23:59:59'])
                    ->select(
                        'events.id',
                        'events.title',
                        'events.description',
                        'events.start_at',
                        'events.end_at',
                        'events.location',
                        'events.status',
                        'clubs.name as club_name',
                        'clubs.code as club_code',
                        'users.name as creator_name',
                        DB::raw('(SELECT COUNT(DISTINCT user_id) FROM event_registrations WHERE event_id = events.id AND status IN ("approved", "attended")) as participant_count')
                    )
                    ->orderBy('events.start_at', 'asc')
                    ->get();
                $filename = 'danh_sach_hoat_dong_' . date('Y-m-d') . '.' . ($format === 'excel' ? 'csv' : 'pdf');
                break;

            case 'violations':
                $data = DB::table('events')
                    ->join('clubs', 'events.club_id', '=', 'clubs.id')
                    ->where(function($q) {
                        $q->where('events.status', 'disabled')
                          ->orWhereNotNull('events.violation_notes')
                          ->orWhere('events.approval_status', 'rejected');
                    })
                    ->whereBetween('events.updated_at', [$startDate, $endDate . ' 23:59:59'])
                    ->select(
                        'events.id',
                        'events.title',
                        'events.start_at',
                        'events.status',
                        'events.violation_notes',
                        'clubs.name as club_name',
                        'clubs.code as club_code'
                    )
                    ->orderBy('events.updated_at', 'desc')
                    ->get();
                $filename = 'danh_sach_hoat_dong_vi_pham_' . date('Y-m-d') . '.' . ($format === 'excel' ? 'csv' : 'pdf');
                break;

            case 'statistics':
                // Thống kê tổng hợp
                $data = [
                    'total_events' => DB::table('events')
                        ->where('approval_status', 'approved')
                        ->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59'])
                        ->count(),
                    'total_participations' => DB::table('event_registrations')
                        ->join('events', 'event_registrations.event_id', '=', 'events.id')
                        ->where('events.approval_status', 'approved')
                        ->whereIn('event_registrations.status', ['approved', 'attended'])
                        ->whereBetween('event_registrations.created_at', [$startDate, $endDate . ' 23:59:59'])
                        ->count(),
                    'by_club' => DB::table('events')
                        ->join('clubs', 'events.club_id', '=', 'clubs.id')
                        ->where('events.approval_status', 'approved')
                        ->whereBetween('events.start_at', [$startDate, $endDate . ' 23:59:59'])
                        ->select(
                            'clubs.name as club_name',
                            'clubs.code as club_code',
                            DB::raw('COUNT(events.id) as event_count')
                        )
                        ->groupBy('clubs.id', 'clubs.name', 'clubs.code')
                        ->orderBy('event_count', 'desc')
                        ->get(),
                ];
                $filename = 'thong_ke_hoat_dong_' . date('Y-m-d') . '.' . ($format === 'excel' ? 'csv' : 'pdf');
                break;
        }

        if ($format === 'excel') {
            return $this->exportToExcel($data, $filename, $reportType, $startDate, $endDate);
        } else {
            return $this->exportToPDF($data, $filename, $reportType, $startDate, $endDate);
        }
    }

    /**
     * Xuất Excel (CSV)
     */
    private function exportToExcel($data, $filename, $reportType, $startDate, $endDate)
    {
        $headers = [];
        $rows = [];

        switch ($reportType) {
            case 'activities':
                $headers = ['ID', 'Tiêu đề', 'CLB', 'Mã CLB', 'Người tạo', 'Bắt đầu', 'Kết thúc', 'Địa điểm', 'Số người tham gia', 'Trạng thái'];
                foreach ($data as $item) {
                    $rows[] = [
                        $item->id,
                        $item->title,
                        $item->club_name,
                        $item->club_code,
                        $item->creator_name ?? '',
                        \Carbon\Carbon::parse($item->start_at)->format('d/m/Y H:i'),
                        \Carbon\Carbon::parse($item->end_at)->format('d/m/Y H:i'),
                        $item->location ?? '',
                        $item->participant_count ?? 0,
                        $item->status === 'ongoing' ? 'Đang diễn ra' : ($item->status === 'finished' ? 'Đã kết thúc' : ($item->status === 'cancelled' ? 'Đã hủy' : 'Sắp diễn ra'))
                    ];
                }
                break;

            case 'violations':
                $headers = ['ID', 'Tiêu đề', 'CLB', 'Mã CLB', 'Thời gian', 'Trạng thái', 'Lý do vi phạm'];
                foreach ($data as $item) {
                    $rows[] = [
                        $item->id,
                        $item->title,
                        $item->club_name,
                        $item->club_code,
                        \Carbon\Carbon::parse($item->start_at)->format('d/m/Y H:i'),
                        $item->status === 'disabled' ? 'Bị vô hiệu hóa' : ($item->approval_status === 'rejected' ? 'Bị từ chối' : 'Vi phạm'),
                        $item->violation_notes ?? 'Không có ghi chú'
                    ];
                }
                break;

            case 'statistics':
                $headers = ['CLB', 'Mã CLB', 'Số hoạt động'];
                foreach ($data['by_club'] as $item) {
                    $rows[] = [
                        $item->club_name,
                        $item->club_code,
                        $item->event_count
                    ];
                }
                break;
        }

        // Tạo CSV content
        $csvContent = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csvContent .= "BÁO CÁO HOẠT ĐỘNG - HỆ THỐNG QUẢN LÝ CLB\n";
        $csvContent .= "Thời gian: " . \Carbon\Carbon::parse($startDate)->format('d/m/Y') . " - " . \Carbon\Carbon::parse($endDate)->format('d/m/Y') . "\n";
        $csvContent .= "Ngày xuất: " . date('d/m/Y H:i') . "\n";
        $csvContent .= "Tổng số bản ghi: " . count($rows) . "\n\n";
        
        // Headers
        $csvHeaders = [];
        foreach ($headers as $header) {
            $csvHeaders[] = '"' . str_replace('"', '""', $header) . '"';
        }
        $csvContent .= implode(',', $csvHeaders) . "\n";

        // Rows
        foreach ($rows as $row) {
            $csvRow = [];
            foreach ($row as $cell) {
                $cell = str_replace('"', '""', (string)$cell);
                $csvRow[] = '"' . $cell . '"';
            }
            $csvContent .= implode(',', $csvRow) . "\n";
        }

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Xuất PDF
     */
    private function exportToPDF($data, $filename, $reportType, $startDate, $endDate)
    {
        $title = '';
        $headers = [];
        $rows = [];

        switch ($reportType) {
            case 'activities':
                $title = 'Danh sách hoạt động';
                $headers = ['ID', 'Tiêu đề', 'CLB', 'Mã CLB', 'Người tạo', 'Bắt đầu', 'Kết thúc', 'Địa điểm', 'Số người tham gia', 'Trạng thái'];
                foreach ($data as $item) {
                    $rows[] = [
                        $item->id,
                        $item->title,
                        $item->club_name,
                        $item->club_code,
                        $item->creator_name ?? '',
                        \Carbon\Carbon::parse($item->start_at)->format('d/m/Y H:i'),
                        \Carbon\Carbon::parse($item->end_at)->format('d/m/Y H:i'),
                        $item->location ?? '',
                        $item->participant_count ?? 0,
                        $item->status === 'ongoing' ? 'Đang diễn ra' : ($item->status === 'finished' ? 'Đã kết thúc' : ($item->status === 'cancelled' ? 'Đã hủy' : 'Sắp diễn ra'))
                    ];
                }
                break;

            case 'violations':
                $title = 'Danh sách hoạt động vi phạm';
                $headers = ['ID', 'Tiêu đề', 'CLB', 'Mã CLB', 'Thời gian', 'Trạng thái', 'Lý do vi phạm'];
                foreach ($data as $item) {
                    $rows[] = [
                        $item->id,
                        $item->title,
                        $item->club_name,
                        $item->club_code,
                        \Carbon\Carbon::parse($item->start_at)->format('d/m/Y H:i'),
                        $item->status === 'disabled' ? 'Bị vô hiệu hóa' : ($item->approval_status === 'rejected' ? 'Bị từ chối' : 'Vi phạm'),
                        $item->violation_notes ?? 'Không có ghi chú'
                    ];
                }
                break;

            case 'statistics':
                $title = 'Thống kê hoạt động theo CLB';
                $headers = ['CLB', 'Mã CLB', 'Số hoạt động'];
                foreach ($data['by_club'] as $item) {
                    $rows[] = [
                        $item->club_name,
                        $item->club_code,
                        $item->event_count
                    ];
                }
                break;
        }

        $html = view('admin.activities.report-pdf', compact('title', 'headers', 'rows', 'startDate', 'endDate', 'data'))->render();

        $pdf = Pdf::loadHTML($html);
        return $pdf->download($filename);
    }

    /**
     * Cập nhật status của hoạt động theo thời gian thực tế
     */
    private function updateActivityStatusByTime($activity)
    {
        // Logic: Nếu đang chờ duyệt (pending), luôn phải là upcoming
        if ($activity->approval_status === 'pending') {
            if ($activity->status !== 'upcoming' && $activity->status !== 'disabled') {
                DB::table('events')->where('id', $activity->id)->update(['status' => 'upcoming']);
                $activity->status = 'upcoming';
            }
            return;
        }
        
        // Chỉ cập nhật status cho các hoạt động đã được duyệt
        if ($activity->approval_status === 'approved' && $activity->start_at && $activity->status !== 'disabled' && $activity->status !== 'cancelled') {
            $startAt = \Carbon\Carbon::parse($activity->start_at);
            $endAt = $activity->end_at ? \Carbon\Carbon::parse($activity->end_at) : $startAt->copy()->addHours(3);
            
            $newStatus = 'upcoming';
            if ($startAt->isPast() && $endAt->isPast()) {
                $newStatus = 'finished';
            } elseif ($startAt->isPast() && $endAt->isFuture()) {
                $newStatus = 'ongoing';
            }
            
            if ($activity->status !== $newStatus) {
                DB::table('events')->where('id', $activity->id)->update(['status' => $newStatus]);
                $activity->status = $newStatus;
            }
        }
    }

    /**
     * Sửa lại status của registrations không hợp lý
     * Logic: Nếu hoạt động chưa kết thúc, không thể có status 'attended'
     */
    private function fixInvalidRegistrationStatuses($eventId, $eventStatus, $endAt)
    {
        // Nếu hoạt động chưa kết thúc (upcoming hoặc ongoing), không thể có 'attended'
        if ($eventStatus === 'upcoming' || $eventStatus === 'ongoing') {
            // Chuyển tất cả 'attended' thành 'approved' (vì chưa thể tham gia)
            DB::table('event_registrations')
                ->where('event_id', $eventId)
                ->where('status', 'attended')
                ->update([
                    'status' => 'approved',
                    'activity_points' => 0, // Xóa điểm vì chưa tham gia
                    'updated_at' => now()
                ]);
        }
        
        // Kiểm tra thêm theo thời gian kết thúc
        if ($endAt) {
            $endDateTime = \Carbon\Carbon::parse($endAt);
            if ($endDateTime->isFuture()) {
                // Nếu chưa đến thời gian kết thúc, không thể có 'attended'
                DB::table('event_registrations')
                    ->where('event_id', $eventId)
                    ->where('status', 'attended')
                    ->update([
                        'status' => 'approved',
                        'activity_points' => 0,
                        'updated_at' => now()
                    ]);
            }
        }
    }

    /**
     * Cập nhật status cho tất cả hoạt động trong hệ thống
     * Method này có thể được gọi từ command hoặc schedule
     */
    public function updateAllActivitiesStatus()
    {
        $activities = DB::table('events')
            ->select('id', 'approval_status', 'status', 'start_at', 'end_at')
            ->where('approval_status', '!=', 'rejected')
            ->get();

        $updated = 0;
        foreach ($activities as $activity) {
            $this->updateActivityStatusByTime($activity);
            $this->fixInvalidRegistrationStatuses($activity->id, $activity->status, $activity->end_at);
            $updated++;
        }

        return $updated;
    }
}

