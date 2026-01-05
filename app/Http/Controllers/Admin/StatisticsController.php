<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use App\Models\Event;
use App\Models\Violation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * Tổng quan hệ thống - Dashboard chuyên nghiệp
     */
    public function overview(Request $request)
    {
        // Lọc theo năm học
        $academicYear = $request->get('academic_year', date('Y'));
        $startDate = $academicYear . '-01-01';
        $endDate = ($academicYear + 1) . '-12-31';
        
        // ========== 1. THỐNG KÊ TỔNG QUAN ==========
        $totalClubs = Club::count();
        $activeClubs = Club::where('status', 'active')->count();
        $archivedClubs = Club::where('status', 'archived')->count();
        $pendingClubs = Club::where('status', 'pending')->count();
        
        // Sinh viên
        $totalMembers = DB::table('club_members')
            ->where('status', 'approved')
            ->distinct()
            ->count('user_id');
        
        $newMembersThisMonth = DB::table('club_members')
            ->where('status', 'approved')
            ->whereYear('joined_date', now()->year)
            ->whereMonth('joined_date', now()->month)
            ->distinct()
            ->count('user_id');
        
        // Hoạt động
        $totalEvents = Event::where('approval_status', 'approved')->count();
        $upcomingEvents = Event::where('approval_status', 'approved')
            ->where('status', 'upcoming')
            ->where('start_at', '>=', now())
            ->count();
        $ongoingEvents = Event::where('approval_status', 'approved')
            ->where('status', 'ongoing')
            ->count();
        $finishedEvents = Event::where('approval_status', 'approved')
            ->where('status', 'finished')
            ->count();
        
        // Đăng ký & Tham gia
        $totalRegistrations = DB::table('event_registrations')->count();
        $totalParticipants = DB::table('event_registrations')
            ->whereIn('status', ['approved', 'attended'])
            ->count();
        
        // Vi phạm
        $totalViolations = Violation::count();
        $pendingViolations = Violation::where('status', 'pending')->count();
        $processedViolations = Violation::where('status', 'processed')->count();
        
        $activityViolations = Event::whereNotNull('violation_notes')
            ->orWhereNotNull('violation_status')
            ->count();
        
        // ========== 2. BIỂU ĐỒ THEO THỜI GIAN ==========
        // Hoạt động theo tháng (12 tháng gần nhất)
        $monthlyEvents = [];
        $monthlyRegistrations = [];
        $monthlyParticipants = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('m/Y');
            $monthLabel = $date->format('M/Y');
            
            $eventsCount = Event::where('approval_status', 'approved')
                ->whereYear('start_at', $date->year)
                ->whereMonth('start_at', $date->month)
                ->count();
            
            $regCount = DB::table('event_registrations')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $partCount = DB::table('event_registrations')
                ->whereIn('status', ['approved', 'attended'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $monthlyEvents[] = ['month' => $monthLabel, 'count' => $eventsCount];
            $monthlyRegistrations[] = ['month' => $monthLabel, 'count' => $regCount];
            $monthlyParticipants[] = ['month' => $monthLabel, 'count' => $partCount];
        }
        
        // Thành viên theo tháng (tăng trưởng)
        $monthlyMembers = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M/Y');
            
            $memberCount = DB::table('club_members')
                ->where('status', 'approved')
                ->where('joined_date', '<=', $date->endOfMonth())
                ->distinct()
                ->count('user_id');
            
            $monthlyMembers[] = ['month' => $monthLabel, 'count' => $memberCount];
        }
        
        // ========== 3. TOP RANKINGS ==========
        // Top 5 CLB có nhiều thành viên nhất
        $topClubsByMembers = DB::table('clubs')
            ->leftJoin('club_members', function($join) {
                $join->on('clubs.id', '=', 'club_members.club_id')
                     ->where('club_members.status', '=', 'approved');
            })
            ->where('clubs.status', 'active')
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                DB::raw('COUNT(DISTINCT club_members.user_id) as member_count')
            )
            ->groupBy('clubs.id', 'clubs.name', 'clubs.code')
            ->orderBy('member_count', 'desc')
            ->limit(5)
            ->get();
        
        // Top 5 CLB có nhiều hoạt động nhất
        $topClubsByEvents = DB::table('clubs')
            ->leftJoin('events', function($join) {
                $join->on('clubs.id', '=', 'events.club_id')
                     ->where('events.approval_status', '=', 'approved');
            })
            ->where('clubs.status', 'active')
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                DB::raw('COUNT(events.id) as event_count')
            )
            ->groupBy('clubs.id', 'clubs.name', 'clubs.code')
            ->orderBy('event_count', 'desc')
            ->limit(5)
            ->get();
        
        // Top 5 hoạt động có nhiều người tham gia nhất
        $topEventsByParticipants = DB::table('events')
            ->leftJoin('event_registrations', function($join) {
                $join->on('events.id', '=', 'event_registrations.event_id')
                     ->whereIn('event_registrations.status', ['approved', 'attended']);
            })
            ->where('events.approval_status', 'approved')
            ->select(
                'events.id',
                'events.title',
                'events.start_at',
                DB::raw('COUNT(event_registrations.id) as participant_count')
            )
            ->groupBy('events.id', 'events.title', 'events.start_at')
            ->orderBy('participant_count', 'desc')
            ->limit(5)
            ->get();
        
        // ========== 4. PHÂN TÍCH & TỶ LỆ ==========
        // Tỷ lệ CLB
        $clubStatusRatio = [
            'active' => $activeClubs,
            'archived' => $archivedClubs,
            'pending' => $pendingClubs,
        ];
        
        // Tỷ lệ trạng thái hoạt động
        $eventStatusRatio = [
            'upcoming' => $upcomingEvents,
            'ongoing' => $ongoingEvents,
            'finished' => $finishedEvents,
            'cancelled' => Event::where('approval_status', 'approved')
                ->where('status', 'cancelled')
                ->count(),
        ];
        
        // Tỷ lệ vi phạm
        $violationStatusRatio = [
            'pending' => $pendingViolations,
            'processed' => $processedViolations,
            'monitoring' => Violation::where('status', 'monitoring')->count(),
        ];
        
        // Phân bố CLB theo lĩnh vực
        $clubsByField = DB::table('clubs')
            ->where('status', 'active')
            ->select('field', DB::raw('COUNT(*) as count'))
            ->groupBy('field')
            ->orderBy('count', 'desc')
            ->get();
        
        // ========== 5. TỶ LỆ THAM GIA ==========
        $participationRate = $totalEvents > 0 
            ? round(($totalParticipants / $totalRegistrations) * 100, 1) 
            : 0;
        
        $avgParticipantsPerEvent = $totalEvents > 0 
            ? round($totalParticipants / $totalEvents, 1) 
            : 0;
        
        // ========== 6. SO SÁNH VỚI THÁNG TRƯỚC ==========
        $lastMonth = now()->subMonth();
        $eventsLastMonth = Event::where('approval_status', 'approved')
            ->whereYear('start_at', $lastMonth->year)
            ->whereMonth('start_at', $lastMonth->month)
            ->count();
        $eventsThisMonth = Event::where('approval_status', 'approved')
            ->whereYear('start_at', now()->year)
            ->whereMonth('start_at', now()->month)
            ->count();
        $eventsGrowth = $eventsLastMonth > 0 
            ? round((($eventsThisMonth - $eventsLastMonth) / $eventsLastMonth) * 100, 1)
            : ($eventsThisMonth > 0 ? 100 : 0);
        
        $membersLastMonth = DB::table('club_members')
            ->where('status', 'approved')
            ->whereYear('joined_date', $lastMonth->year)
            ->whereMonth('joined_date', $lastMonth->month)
            ->distinct()
            ->count('user_id');
        $membersGrowth = $membersLastMonth > 0 
            ? round((($newMembersThisMonth - $membersLastMonth) / $membersLastMonth) * 100, 1)
            : ($newMembersThisMonth > 0 ? 100 : 0);
        
        return view('admin.statistics.overview', compact(
            // Tổng quan
            'totalClubs', 'activeClubs', 'archivedClubs', 'pendingClubs',
            'totalMembers', 'newMembersThisMonth',
            'totalEvents', 'upcomingEvents', 'ongoingEvents', 'finishedEvents',
            'totalRegistrations', 'totalParticipants',
            'totalViolations', 'pendingViolations', 'processedViolations', 'activityViolations',
            // Biểu đồ
            'monthlyEvents', 'monthlyRegistrations', 'monthlyParticipants', 'monthlyMembers',
            // Rankings
            'topClubsByMembers', 'topClubsByEvents', 'topEventsByParticipants',
            // Tỷ lệ
            'clubStatusRatio', 'eventStatusRatio', 'violationStatusRatio', 'clubsByField',
            // Phân tích
            'participationRate', 'avgParticipantsPerEvent',
            'eventsGrowth', 'membersGrowth',
            // Filter
            'academicYear'
        ));
    }

    /**
     * Thống kê câu lạc bộ - Dashboard chuyên nghiệp
     */
    public function clubs(Request $request)
    {
        $field = $request->get('field'); // Lọc theo lĩnh vực
        
        // ========== 1. THỐNG KÊ TỔNG QUAN ==========
        $totalClubs = Club::count();
        $activeClubs = Club::where('status', 'active')->count();
        $archivedClubs = Club::where('status', 'archived')->count();
        $pendingClubs = Club::where('status', 'pending')->count();
        
        // Tổng thành viên
        $totalMembers = DB::table('club_members')
            ->where('status', 'approved')
            ->distinct()
            ->count('user_id');
        
        // Tổng hoạt động
        $totalEvents = Event::where('approval_status', 'approved')->count();
        
        // CLB đang hoạt động (có filter)
        $activeClubsQuery = Club::where('status', 'active')
            ->when($field, function($query) use ($field) {
                return $query->where('field', 'like', "%{$field}%");
            });
        $activeClubsList = $activeClubsQuery->get();
        
        // CLB tạm dừng
        $archivedClubsList = Club::where('status', 'archived')->get();
        
        // ========== 2. TOP RANKINGS ==========
        // Top 10 CLB có nhiều thành viên nhất
        $topClubsByMembers = DB::table('clubs')
            ->leftJoin('club_members', function($join) {
                $join->on('clubs.id', '=', 'club_members.club_id')
                     ->where('club_members.status', '=', 'approved');
            })
            ->where('clubs.status', 'active')
            ->when($field, function($query) use ($field) {
                return $query->where('clubs.field', 'like', "%{$field}%");
            })
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                'clubs.field',
                DB::raw('COUNT(DISTINCT club_members.user_id) as member_count')
            )
            ->groupBy('clubs.id', 'clubs.name', 'clubs.code', 'clubs.field')
            ->orderBy('member_count', 'desc')
            ->limit(10)
            ->get();
        
        // Top 10 CLB có số lượng hoạt động nhiều nhất
        $topClubsByEvents = DB::table('clubs')
            ->leftJoin('events', function($join) {
                $join->on('clubs.id', '=', 'events.club_id')
                     ->where('events.approval_status', '=', 'approved');
            })
            ->where('clubs.status', 'active')
            ->when($field, function($query) use ($field) {
                return $query->where('clubs.field', 'like', "%{$field}%");
            })
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                'clubs.field',
                DB::raw('COUNT(events.id) as event_count')
            )
            ->groupBy('clubs.id', 'clubs.name', 'clubs.code', 'clubs.field')
            ->orderBy('event_count', 'desc')
            ->limit(10)
            ->get();
        
        // Top 10 CLB có nhiều người tham gia hoạt động nhất
        $topClubsByParticipants = DB::table('clubs')
            ->leftJoin('events', function($join) {
                $join->on('clubs.id', '=', 'events.club_id')
                     ->where('events.approval_status', '=', 'approved');
            })
            ->leftJoin('event_registrations', function($join) {
                $join->on('events.id', '=', 'event_registrations.event_id')
                     ->whereIn('event_registrations.status', ['approved', 'attended']);
            })
            ->where('clubs.status', 'active')
            ->when($field, function($query) use ($field) {
                return $query->where('clubs.field', 'like', "%{$field}%");
            })
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                'clubs.field',
                DB::raw('COUNT(DISTINCT event_registrations.user_id) as participant_count')
            )
            ->groupBy('clubs.id', 'clubs.name', 'clubs.code', 'clubs.field')
            ->orderBy('participant_count', 'desc')
            ->limit(10)
            ->get();
        
        // ========== 3. BIỂU ĐỒ & PHÂN TÍCH ==========
        // Tần suất hoạt động theo tháng (12 tháng gần nhất)
        $activityFrequency = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M/Y');
            $count = Event::where('approval_status', 'approved')
                ->whereYear('start_at', $date->year)
                ->whereMonth('start_at', $date->month)
                ->count();
            $activityFrequency[] = ['month' => $monthLabel, 'count' => $count];
        }
        
        // Phân bố CLB theo lĩnh vực
        $clubsByField = DB::table('clubs')
            ->where('status', 'active')
            ->when($field, function($query) use ($field) {
                return $query->where('field', 'like', "%{$field}%");
            })
            ->select('field', DB::raw('COUNT(*) as count'))
            ->groupBy('field')
            ->orderBy('count', 'desc')
            ->get();
        
        // Tỷ lệ trạng thái CLB
        $clubStatusRatio = [
            'active' => $activeClubs,
            'archived' => $archivedClubs,
            'pending' => $pendingClubs,
        ];
        
        // Thống kê thành viên theo CLB (trung bình)
        $avgMembersPerClub = $activeClubs > 0 
            ? round($totalMembers / $activeClubs, 1) 
            : 0;
        
        // Thống kê hoạt động theo CLB (trung bình)
        $avgEventsPerClub = $activeClubs > 0 
            ? round($totalEvents / $activeClubs, 1) 
            : 0;
        
        // CLB mới nhất (5 CLB)
        $newestClubs = Club::where('status', 'active')
            ->when($field, function($query) use ($field) {
                return $query->where('field', 'like', "%{$field}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Danh sách tất cả CLB với thống kê (có phân trang)
        $allClubs = DB::table('clubs')
            ->leftJoin(DB::raw('(SELECT club_id, COUNT(*) as event_count FROM events WHERE approval_status = "approved" GROUP BY club_id) as event_stats'), 'clubs.id', '=', 'event_stats.club_id')
            ->leftJoin(DB::raw('(SELECT club_id, COUNT(DISTINCT user_id) as member_count FROM club_members WHERE status = "approved" GROUP BY club_id) as member_stats'), 'clubs.id', '=', 'member_stats.club_id')
            ->when($field, function($query) use ($field) {
                return $query->where('clubs.field', 'like', "%{$field}%");
            })
            ->select(
                'clubs.id',
                'clubs.code',
                'clubs.name',
                'clubs.field',
                'clubs.status',
                DB::raw('COALESCE(event_stats.event_count, 0) as event_count'),
                DB::raw('COALESCE(member_stats.member_count, 0) as member_count')
            )
            ->orderBy('member_count', 'desc')
            ->orderBy('event_count', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        // Lấy danh sách lĩnh vực để filter
        $availableFields = DB::table('clubs')
            ->where('status', 'active')
            ->whereNotNull('field')
            ->distinct()
            ->pluck('field')
            ->filter()
            ->values();
        
        return view('admin.statistics.clubs', compact(
            // Tổng quan
            'totalClubs', 'activeClubs', 'archivedClubs', 'pendingClubs',
            'totalMembers', 'totalEvents',
            'activeClubsList', 'archivedClubsList',
            // Rankings
            'topClubsByMembers', 'topClubsByEvents', 'topClubsByParticipants',
            // Biểu đồ & Phân tích
            'activityFrequency', 'clubsByField', 'clubStatusRatio',
            // Thống kê
            'avgMembersPerClub', 'avgEventsPerClub', 'newestClubs',
            // Danh sách
            'allClubs', 'availableFields',
            // Filter
            'field'
        ));
    }

    /**
     * Thống kê thành viên - Dashboard chuyên nghiệp
     */
    public function members(Request $request)
    {
        // Lọc theo năm học
        $academicYear = $request->get('academic_year', date('Y'));
        $startDate = $academicYear . '-01-01';
        $endDate = ($academicYear + 1) . '-12-31';
        
        $department = $request->get('department');
        $class = $request->get('class');
        
        // ========== 1. THỐNG KÊ TỔNG QUAN ==========
        // Tổng số sinh viên tham gia CLB
        $totalMembers = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.status', 'approved')
            ->when($department, function($query) use ($department) {
                return $query->where('users.department', 'like', "%{$department}%");
            })
            ->when($class, function($query) use ($class) {
                return $query->where('users.class', 'like', "%{$class}%");
            })
            ->distinct()
            ->count('users.id');
        
        // Thành viên mới trong năm học
        $newMembersThisYear = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.status', 'approved')
            ->whereBetween('club_members.joined_date', [$startDate, $endDate])
            ->when($department, function($query) use ($department) {
                return $query->where('users.department', 'like', "%{$department}%");
            })
            ->when($class, function($query) use ($class) {
                return $query->where('users.class', 'like', "%{$class}%");
            })
            ->distinct()
            ->count('users.id');
        
        // Thành viên tham gia nhiều CLB
        $membersInMultipleClubsCount = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.status', 'approved')
            ->when($department, function($query) use ($department) {
                return $query->where('users.department', 'like', "%{$department}%");
            })
            ->when($class, function($query) use ($class) {
                return $query->where('users.class', 'like', "%{$class}%");
            })
            ->select('users.id', DB::raw('COUNT(DISTINCT club_members.club_id) as club_count'))
            ->groupBy('users.id')
            ->having('club_count', '>', 1)
            ->get()
            ->count();
        
        // ========== 2. BIỂU ĐỒ & PHÂN TÍCH ==========
        // Tỷ lệ sinh viên tham gia CLB theo khoa
        $membersByDepartment = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.status', 'approved')
            ->when($department, function($query) use ($department) {
                return $query->where('users.department', 'like', "%{$department}%");
            })
            ->when($class, function($query) use ($class) {
                return $query->where('users.class', 'like', "%{$class}%");
            })
            ->select('users.department', DB::raw('COUNT(DISTINCT users.id) as count'))
            ->groupBy('users.department')
            ->orderBy('count', 'desc')
            ->get();
        
        // Tỷ lệ sinh viên tham gia CLB theo lớp (Top 20)
        $membersByClass = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.status', 'approved')
            ->when($department, function($query) use ($department) {
                return $query->where('users.department', 'like', "%{$department}%");
            })
            ->when($class, function($query) use ($class) {
                return $query->where('users.class', 'like', "%{$class}%");
            })
            ->select('users.class', DB::raw('COUNT(DISTINCT users.id) as count'))
            ->groupBy('users.class')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();
        
        // Tăng trưởng thành viên theo tháng (12 tháng gần nhất)
        $monthlyMembers = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M/Y');
            
            $memberCount = DB::table('club_members')
                ->join('users', 'club_members.user_id', '=', 'users.id')
                ->where('club_members.status', 'approved')
                ->where('club_members.joined_date', '<=', $date->endOfMonth())
                ->when($department, function($query) use ($department) {
                    return $query->where('users.department', 'like', "%{$department}%");
                })
                ->when($class, function($query) use ($class) {
                    return $query->where('users.class', 'like', "%{$class}%");
                })
                ->distinct()
                ->count('users.id');
            
            $monthlyMembers[] = ['month' => $monthLabel, 'count' => $memberCount];
        }
        
        // Vai trò trong CLB
        $rolesDistribution = DB::table('club_members')
            ->where('status', 'approved')
            ->select('position', DB::raw('COUNT(*) as count'))
            ->groupBy('position')
            ->orderBy('count', 'desc')
            ->get();
        
        // ========== 3. TOP RANKINGS ==========
        // Sinh viên tham gia nhiều CLB nhất (Top 10)
        $membersInMultipleClubs = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.status', 'approved')
            ->when($department, function($query) use ($department) {
                return $query->where('users.department', 'like', "%{$department}%");
            })
            ->when($class, function($query) use ($class) {
                return $query->where('users.class', 'like', "%{$class}%");
            })
            ->select(
                'users.id',
                'users.name',
                'users.student_code',
                'users.department',
                'users.class',
                DB::raw('COUNT(DISTINCT club_members.club_id) as club_count')
            )
            ->groupBy('users.id', 'users.name', 'users.student_code', 'users.department', 'users.class')
            ->having('club_count', '>', 1)
            ->orderBy('club_count', 'desc')
            ->limit(10)
            ->get();
        
        // Thành viên tích cực (tham gia nhiều hoạt động nhất - Top 10)
        $activeMembers = DB::table('event_registrations')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('event_registrations.status', 'attended')
            ->where('events.approval_status', 'approved')
            ->when($department, function($query) use ($department) {
                return $query->where('users.department', 'like', "%{$department}%");
            })
            ->when($class, function($query) use ($class) {
                return $query->where('users.class', 'like', "%{$class}%");
            })
            ->select(
                'users.id',
                'users.name',
                'users.student_code',
                'users.department',
                'users.class',
                DB::raw('COUNT(event_registrations.id) as participation_count')
            )
            ->groupBy('users.id', 'users.name', 'users.student_code', 'users.department', 'users.class')
            ->orderBy('participation_count', 'desc')
            ->limit(10)
            ->get();
        
        // Lấy danh sách khoa và lớp để filter
        $availableDepartments = DB::table('users')
            ->join('club_members', 'users.id', '=', 'club_members.user_id')
            ->where('club_members.status', 'approved')
            ->whereNotNull('users.department')
            ->distinct()
            ->pluck('users.department')
            ->filter()
            ->values();
        
        $availableClasses = DB::table('users')
            ->join('club_members', 'users.id', '=', 'club_members.user_id')
            ->where('club_members.status', 'approved')
            ->whereNotNull('users.class')
            ->distinct()
            ->pluck('users.class')
            ->filter()
            ->values();
        
        return view('admin.statistics.members', compact(
            // Tổng quan
            'totalMembers', 'newMembersThisYear', 'membersInMultipleClubsCount',
            // Biểu đồ
            'membersByDepartment', 'membersByClass', 'monthlyMembers', 'rolesDistribution',
            // Rankings
            'membersInMultipleClubs', 'activeMembers',
            // Filter
            'academicYear', 'department', 'class', 'availableDepartments', 'availableClasses'
        ));
    }

    /**
     * Thống kê hoạt động - sự kiện - Dashboard chuyên nghiệp
     */
    public function activities(Request $request)
    {
        // Lọc theo năm học
        $academicYear = $request->get('academic_year', date('Y'));
        $startDate = $request->get('start_date', $academicYear . '-01-01');
        $endDate = $request->get('end_date', ($academicYear + 1) . '-12-31');
        
        $clubId = $request->get('club_id');
        
        // ========== 1. THỐNG KÊ TỔNG QUAN ==========
        $totalEvents = Event::where('approval_status', 'approved')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->count();
        
        $upcomingEvents = Event::where('approval_status', 'approved')
            ->where('status', 'upcoming')
            ->where('start_at', '>=', now())
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->count();
        
        $ongoingEvents = Event::where('approval_status', 'approved')
            ->where('status', 'ongoing')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->count();
        
        $finishedEvents = Event::where('approval_status', 'approved')
            ->where('status', 'finished')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->count();
        
        $cancelledEvents = Event::where('approval_status', 'approved')
            ->where('status', 'cancelled')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->count();
        
        $violatedEvents = Event::where('approval_status', 'approved')
            ->where(function($query) {
                $query->whereNotNull('violation_notes')
                      ->orWhereNotNull('violation_status');
            })
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->count();
        
        // Tổng đăng ký & tham gia
        $totalRegistrations = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.approval_status', 'approved')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('events.club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('events.start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->count();
        
        $totalParticipants = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.approval_status', 'approved')
            ->whereIn('event_registrations.status', ['approved', 'attended'])
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('events.club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('events.start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->count();
        
        $participationRate = $totalRegistrations > 0 
            ? round(($totalParticipants / $totalRegistrations) * 100, 1) 
            : 0;
        
        // ========== 2. BIỂU ĐỒ & PHÂN TÍCH ==========
        // Tổng số hoạt động theo từng CLB
        $eventsByClub = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('events.approval_status', 'approved')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('events.club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('events.start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->select(
                'clubs.id as club_id',
                'clubs.name as club_name',
                'clubs.code as club_code',
                DB::raw('COUNT(events.id) as event_count')
            )
            ->groupBy('clubs.id', 'clubs.name', 'clubs.code')
            ->orderBy('event_count', 'desc')
            ->get();
        
        // Số hoạt động theo tháng (12 tháng gần nhất)
        $eventsByMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M/Y');
            $count = Event::where('approval_status', 'approved')
                ->when($clubId, function($query) use ($clubId) {
                    return $query->where('club_id', $clubId);
                })
                ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                    return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
                })
                ->whereYear('start_at', $date->year)
                ->whereMonth('start_at', $date->month)
                ->count();
            $eventsByMonth[] = ['month' => $monthLabel, 'count' => $count];
        }
        
        // Tỷ lệ trạng thái hoạt động
        $eventStatusRatio = [
            'upcoming' => $upcomingEvents,
            'ongoing' => $ongoingEvents,
            'finished' => $finishedEvents,
            'cancelled' => $cancelledEvents,
        ];
        
        // ========== 3. TOP RANKINGS ==========
        // Top 10 hoạt động có nhiều người tham gia nhất
        $topEventsByParticipants = DB::table('events')
            ->leftJoin('event_registrations', function($join) {
                $join->on('events.id', '=', 'event_registrations.event_id')
                     ->whereIn('event_registrations.status', ['approved', 'attended']);
            })
            ->where('events.approval_status', 'approved')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('events.club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('events.start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->select(
                'events.id',
                'events.title',
                'events.start_at',
                'events.club_id',
                DB::raw('COUNT(event_registrations.id) as participant_count')
            )
            ->groupBy('events.id', 'events.title', 'events.start_at', 'events.club_id')
            ->orderBy('participant_count', 'desc')
            ->limit(10)
            ->get();
        
        // Danh sách hoạt động đã tổ chức (có phân trang)
        $completedEvents = Event::where('approval_status', 'approved')
            ->where('status', 'finished')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->with('club')
            ->orderBy('start_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        // Lấy danh sách CLB để filter
        $clubs = Club::where('status', 'active')->orderBy('name')->get();
        
        return view('admin.statistics.activities', compact(
            // Tổng quan
            'totalEvents', 'upcomingEvents', 'ongoingEvents', 'finishedEvents', 
            'cancelledEvents', 'violatedEvents',
            'totalRegistrations', 'totalParticipants', 'participationRate',
            // Biểu đồ
            'eventsByClub', 'eventsByMonth', 'eventStatusRatio',
            // Rankings
            'topEventsByParticipants',
            // Danh sách
            'completedEvents',
            // Filter
            'academicYear', 'startDate', 'endDate', 'clubId', 'clubs'
        ));
    }

    /**
     * Thống kê vi phạm - kỷ luật - Dashboard chuyên nghiệp
     */
    public function violations(Request $request)
    {
        // Lọc theo năm học
        $academicYear = $request->get('academic_year', date('Y'));
        $startDate = $request->get('start_date', $academicYear . '-01-01');
        $endDate = $request->get('end_date', ($academicYear + 1) . '-12-31');
        
        $clubId = $request->get('club_id');
        
        // ========== 1. THỐNG KÊ TỔNG QUAN ==========
        // Tổng số vụ vi phạm (sinh viên)
        $totalViolations = Violation::when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('violation_date', [$startDate, $endDate]);
            })
            ->count();
        
        // Tổng số hoạt động vi phạm
        $totalActivityViolations = Event::where(function($query) {
                $query->whereNotNull('violation_notes')
                      ->orWhereNotNull('violation_status');
            })
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->count();
        
        // Vi phạm chưa xử lý
        $pendingViolations = Violation::where('status', 'pending')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('violation_date', [$startDate, $endDate]);
            })
            ->count();
        
        // Vi phạm đã xử lý
        $processedViolations = Violation::where('status', 'processed')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('violation_date', [$startDate, $endDate]);
            })
            ->count();
        
        // Vi phạm đang theo dõi
        $monitoringViolations = Violation::where('status', 'monitoring')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('violation_date', [$startDate, $endDate]);
            })
            ->count();
        
        // ========== 2. BIỂU ĐỒ & PHÂN TÍCH ==========
        // Số vi phạm theo từng CLB
        $violationsByClub = DB::table('violations')
            ->join('clubs', 'violations.club_id', '=', 'clubs.id')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('violations.club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('violations.violation_date', [$startDate, $endDate]);
            })
            ->select(
                'clubs.id as club_id',
                'clubs.name as club_name',
                'clubs.code as club_code',
                DB::raw('COUNT(violations.id) as violation_count')
            )
            ->groupBy('clubs.id', 'clubs.name', 'clubs.code')
            ->orderBy('violation_count', 'desc')
            ->get();
        
        // Phân loại vi phạm theo mức độ
        $violationsBySeverity = DB::table('violations')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('violation_date', [$startDate, $endDate]);
            })
            ->select('severity', DB::raw('COUNT(*) as count'))
            ->groupBy('severity')
            ->orderBy('count', 'desc')
            ->get();
        
        // Vi phạm theo tháng (12 tháng gần nhất)
        $monthlyViolations = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M/Y');
            
            $count = Violation::when($clubId, function($query) use ($clubId) {
                    return $query->where('club_id', $clubId);
                })
                ->whereYear('violation_date', $date->year)
                ->whereMonth('violation_date', $date->month)
                ->count();
            
            $monthlyViolations[] = ['month' => $monthLabel, 'count' => $count];
        }
        
        // Tỷ lệ trạng thái vi phạm
        $violationStatusRatio = [
            'pending' => $pendingViolations,
            'processed' => $processedViolations,
            'monitoring' => $monitoringViolations,
        ];
        
        // ========== 3. TOP RANKINGS ==========
        // Top 10 CLB có nhiều vi phạm nhất
        $topClubsByViolations = DB::table('violations')
            ->join('clubs', 'violations.club_id', '=', 'clubs.id')
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('violations.violation_date', [$startDate, $endDate]);
            })
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                DB::raw('COUNT(violations.id) as violation_count')
            )
            ->groupBy('clubs.id', 'clubs.name', 'clubs.code')
            ->orderBy('violation_count', 'desc')
            ->limit(10)
            ->get();
        
        // Danh sách vi phạm (có phân trang)
        $violations = Violation::with(['user', 'club', 'regulation'])
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('violation_date', [$startDate, $endDate]);
            })
            ->orderBy('violation_date', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        // Lấy danh sách CLB để filter
        $clubs = Club::where('status', 'active')->orderBy('name')->get();
        
        return view('admin.statistics.violations', compact(
            // Tổng quan
            'totalViolations', 'totalActivityViolations',
            'pendingViolations', 'processedViolations', 'monitoringViolations',
            // Biểu đồ
            'violationsByClub', 'violationsBySeverity', 'monthlyViolations', 'violationStatusRatio',
            // Rankings
            'topClubsByViolations',
            // Danh sách
            'violations',
            // Filter
            'academicYear', 'startDate', 'endDate', 'clubId', 'clubs'
        ));
    }

    /**
     * Báo cáo tài chính CLB - Dashboard chuyên nghiệp
     */
    public function financial(Request $request)
    {
        // Lọc theo năm học
        $academicYear = $request->get('academic_year', date('Y'));
        $startDate = $request->get('start_date', $academicYear . '-01-01');
        $endDate = $request->get('end_date', ($academicYear + 1) . '-12-31');
        
        $clubId = $request->get('club_id');
        
        // ========== 1. THỐNG KÊ TỔNG QUAN ==========
        // Tổng kinh phí thu (từ các hoạt động có expected_budget)
        $totalRevenue = Event::where('approval_status', 'approved')
            ->whereNotNull('expected_budget')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->sum('expected_budget');
        
        // Tổng kinh phí chi (tạm thời dùng expected_budget * 0.8 như chi phí thực tế)
        // TODO: Nếu có bảng expenses riêng, cần query từ đó
        $totalExpenses = Event::where('approval_status', 'approved')
            ->whereNotNull('expected_budget')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->sum(DB::raw('expected_budget * 0.8'));
        
        // Số dư
        $totalBalance = $totalRevenue - $totalExpenses;
        
        // ========== 2. BIỂU ĐỒ & PHÂN TÍCH ==========
        // Số dư theo từng CLB
        $balanceByClub = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('events.approval_status', 'approved')
            ->whereNotNull('events.expected_budget')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('events.club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('events.start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->select(
                'clubs.id as club_id',
                'clubs.name as club_name',
                'clubs.code as club_code',
                DB::raw('COALESCE(SUM(events.expected_budget), 0) as total_revenue'),
                DB::raw('COALESCE(SUM(events.expected_budget * 0.8), 0) as total_expenses'),
                DB::raw('COALESCE(SUM(events.expected_budget * 0.2), 0) as balance')
            )
            ->groupBy('clubs.id', 'clubs.name', 'clubs.code')
            ->orderBy('total_revenue', 'desc')
            ->get();
        
        // Thu - chi theo thời gian (12 tháng gần nhất)
        $financialByMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M/Y');
            
            $revenue = Event::where('approval_status', 'approved')
                ->whereNotNull('expected_budget')
                ->when($clubId, function($query) use ($clubId) {
                    return $query->where('club_id', $clubId);
                })
                ->whereYear('start_at', $date->year)
                ->whereMonth('start_at', $date->month)
                ->sum('expected_budget');
            
            $expenses = $revenue * 0.8; // Tạm thời
            
            $financialByMonth[] = [
                'month' => $monthLabel,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'balance' => $revenue - $expenses,
            ];
        }
        
        // ========== 3. TOP RANKINGS ==========
        // Top 10 CLB có kinh phí cao nhất
        $topClubsByRevenue = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('events.approval_status', 'approved')
            ->whereNotNull('events.expected_budget')
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('events.club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('events.start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                DB::raw('COALESCE(SUM(events.expected_budget), 0) as total_revenue')
            )
            ->groupBy('clubs.id', 'clubs.name', 'clubs.code')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();
        
        // Danh sách hoạt động có kinh phí (có phân trang)
        $eventsWithBudget = Event::where('approval_status', 'approved')
            ->whereNotNull('expected_budget')
            ->where('expected_budget', '>', 0)
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->with('club')
            ->orderBy('start_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        // Lấy danh sách CLB để filter
        $clubs = Club::where('status', 'active')->orderBy('name')->get();
        
        return view('admin.statistics.financial', compact(
            // Tổng quan
            'totalRevenue', 'totalExpenses', 'totalBalance',
            // Biểu đồ
            'balanceByClub', 'financialByMonth',
            // Rankings
            'topClubsByRevenue',
            // Danh sách
            'eventsWithBudget',
            // Filter
            'academicYear', 'startDate', 'endDate', 'clubId', 'clubs'
        ));
    }

    /**
     * Xuất báo cáo
     */
    public function export(Request $request)
    {
        // Lấy danh sách CLB để filter
        $clubs = Club::where('status', 'active')->orderBy('name')->get();
        
        return view('admin.statistics.export', compact('clubs'));
    }

    /**
     * Generate export report
     */
    public function generateExport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:club_overview,members,activities,participations,violations',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel',
            'club_id' => 'nullable|exists:clubs,id',
        ]);

        $user = Auth::user();
        $reportType = $request->report_type;
        $startDate = $request->start_date;
        $endDate = $request->end_date . ' 23:59:59';
        $format = $request->format;
        $clubId = $request->club_id;

        // Nếu là Chủ nhiệm, chỉ cho phép xuất báo cáo của CLB mình
        if ($user->role_id == 3) { // Chairman role
            $chairmanClub = DB::table('club_members')
                ->where('user_id', $user->id)
                ->where('position', 'chairman')
                ->where('status', 'approved')
                ->first();
            
            if (!$chairmanClub) {
                return back()->with('error', 'Bạn không có quyền xuất báo cáo.');
            }
            
            $clubId = $chairmanClub->club_id; // Force club_id to chairman's club
        }

        $data = [];
        $filename = '';

        switch ($reportType) {
            case 'club_overview':
                $data = $this->getClubOverviewData($clubId, $startDate, $endDate);
                $filename = 'bao_cao_tong_quan_clb_' . date('Y-m-d_His') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
                break;

            case 'members':
                $data = $this->getMembersData($clubId, $startDate, $endDate);
                $filename = 'bao_cao_danh_sach_thanh_vien_' . date('Y-m-d_His') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
                break;

            case 'activities':
                $data = $this->getActivitiesData($clubId, $startDate, $endDate);
                $filename = 'bao_cao_hoat_dong_clb_' . date('Y-m-d_His') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
                break;

            case 'participations':
                $data = $this->getParticipationsData($clubId, $startDate, $endDate);
                $filename = 'bao_cao_tham_gia_hoat_dong_' . date('Y-m-d_His') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
                break;

            case 'violations':
                $data = $this->getViolationsData($clubId, $startDate, $endDate);
                $filename = 'bao_cao_vi_pham_ky_luat_' . date('Y-m-d_His') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
                break;
        }

        if ($format === 'excel') {
            return $this->exportToExcel($data, $filename, $reportType, $startDate, $endDate);
        } else {
            return $this->exportToPDF($data, $filename, $reportType, $startDate, $endDate);
        }
    }

    /**
     * Get Club Overview Data
     */
    private function getClubOverviewData($clubId, $startDate, $endDate)
    {
        $query = Club::query();
        
        if ($clubId) {
            $query->where('id', $clubId);
        }

        $clubs = $query->get();
        $data = [];

        foreach ($clubs as $club) {
            $totalMembers = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('status', 'approved')
                ->distinct()
                ->count('user_id');

            $totalEvents = Event::where('club_id', $club->id)
                ->where('approval_status', 'approved')
                ->whereBetween('start_at', [$startDate, $endDate])
                ->count();

            $totalRevenue = Event::where('club_id', $club->id)
                ->where('approval_status', 'approved')
                ->whereNotNull('expected_budget')
                ->whereBetween('start_at', [$startDate, $endDate])
                ->sum('expected_budget');

            $data[] = [
                'club_code' => $club->code,
                'club_name' => $club->name,
                'field' => $club->field,
                'total_members' => $totalMembers,
                'total_events' => $totalEvents,
                'total_revenue' => $totalRevenue,
                'status' => $club->status === 'active' ? 'Đang hoạt động' : 'Tạm dừng',
            ];
        }

        return $data;
    }

    /**
     * Get Members Data
     */
    private function getMembersData($clubId, $startDate, $endDate)
    {
        $query = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.status', 'approved')
            ->whereBetween('club_members.joined_date', [$startDate, $endDate])
            ->select(
                'users.student_code',
                'users.name',
                'users.email',
                'users.department',
                'users.class',
                'club_members.position',
                'club_members.joined_date',
                'clubs.name as club_name',
                'clubs.code as club_code'
            );

        if ($clubId) {
            $query->where('club_members.club_id', $clubId);
        }

        return $query->orderBy('club_members.joined_date', 'desc')->get();
    }

    /**
     * Get Activities Data
     */
    private function getActivitiesData($clubId, $startDate, $endDate)
    {
        $query = Event::where('approval_status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate])
            ->with('club');

        if ($clubId) {
            $query->where('club_id', $clubId);
        }

        $events = $query->orderBy('start_at', 'desc')->get();
        $data = [];

        foreach ($events as $event) {
            $participantCount = DB::table('event_registrations')
                ->where('event_id', $event->id)
                ->whereIn('status', ['approved', 'attended'])
                ->count();

            $data[] = [
                'title' => $event->title,
                'club_name' => $event->club->name ?? 'N/A',
                'club_code' => $event->club->code ?? 'N/A',
                'start_at' => $event->start_at,
                'end_at' => $event->end_at,
                'location' => $event->location,
                'status' => $event->status,
                'participant_count' => $participantCount,
            ];
        }

        return $data;
    }

    /**
     * Get Participations Data
     */
    private function getParticipationsData($clubId, $startDate, $endDate)
    {
        $query = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('events.approval_status', 'approved')
            ->whereBetween('events.start_at', [$startDate, $endDate])
            ->select(
                'users.student_code',
                'users.name',
                'events.title as event_title',
                'clubs.name as club_name',
                'clubs.code as club_code',
                'event_registrations.status',
                'event_registrations.created_at as registered_at'
            );

        if ($clubId) {
            $query->where('events.club_id', $clubId);
        }

        return $query->orderBy('event_registrations.created_at', 'desc')->get();
    }

    /**
     * Get Violations Data
     */
    private function getViolationsData($clubId, $startDate, $endDate)
    {
        // Activity violations
        $activityViolations = Event::where(function($query) {
                $query->whereNotNull('violation_notes')
                      ->orWhereNotNull('violation_status');
            })
            ->whereBetween('start_at', [$startDate, $endDate])
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->with('club')
            ->get();

        // Student violations
        $studentViolations = Violation::whereBetween('violation_date', [$startDate, $endDate])
            ->when($clubId, function($query) use ($clubId) {
                return $query->where('club_id', $clubId);
            })
            ->with(['user', 'club', 'regulation'])
            ->get();

        return [
            'activity_violations' => $activityViolations,
            'student_violations' => $studentViolations,
        ];
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($data, $filename, $reportType, $startDate, $endDate)
    {
        $headers = [];
        $rows = [];

        switch ($reportType) {
            case 'club_overview':
                $headers = ['Mã CLB', 'Tên CLB', 'Lĩnh vực', 'Số thành viên', 'Số hoạt động', 'Tổng kinh phí (VNĐ)', 'Trạng thái'];
                foreach ($data as $item) {
                    $rows[] = [
                        $item['club_code'],
                        $item['club_name'],
                        $item['field'] ?? 'N/A',
                        $item['total_members'],
                        $item['total_events'],
                        number_format($item['total_revenue'], 0, ',', '.'),
                        $item['status'],
                    ];
                }
                break;

            case 'members':
                $headers = ['MSSV', 'Họ tên', 'Email', 'Khoa', 'Lớp', 'Chức vụ', 'Ngày tham gia', 'CLB'];
                foreach ($data as $item) {
                    $positionMap = [
                        'chairman' => 'Chủ nhiệm',
                        'vice_chairman' => 'Phó chủ nhiệm',
                        'secretary' => 'Thư ký CLB',
                        'head_expertise' => 'Trưởng ban Chuyên môn',
                        'head_media' => 'Trưởng ban Truyền thông',
                        'head_events' => 'Trưởng ban Hoạt động',
                        'treasurer' => 'Trưởng ban Tài chính',
                        'member' => 'Thành viên',
                    ];
                    $rows[] = [
                        $item->student_code,
                        $item->name,
                        $item->email,
                        $item->department ?? 'N/A',
                        $item->class ?? 'N/A',
                        $positionMap[$item->position] ?? $item->position,
                        \Carbon\Carbon::parse($item->joined_date)->format('d/m/Y'),
                        $item->club_name . ' (' . $item->club_code . ')',
                    ];
                }
                break;

            case 'activities':
                $headers = ['Tên hoạt động', 'CLB', 'Mã CLB', 'Bắt đầu', 'Kết thúc', 'Địa điểm', 'Số người tham gia', 'Trạng thái'];
                foreach ($data as $item) {
                    $statusMap = [
                        'upcoming' => 'Sắp diễn ra',
                        'ongoing' => 'Đang diễn ra',
                        'finished' => 'Đã kết thúc',
                        'cancelled' => 'Đã hủy',
                    ];
                    $rows[] = [
                        $item['title'],
                        $item['club_name'],
                        $item['club_code'],
                        \Carbon\Carbon::parse($item['start_at'])->format('d/m/Y H:i'),
                        \Carbon\Carbon::parse($item['end_at'])->format('d/m/Y H:i'),
                        $item['location'] ?? 'N/A',
                        $item['participant_count'],
                        $statusMap[$item['status']] ?? $item['status'],
                    ];
                }
                break;

            case 'participations':
                $headers = ['MSSV', 'Họ tên', 'Tên hoạt động', 'CLB', 'Mã CLB', 'Trạng thái', 'Ngày đăng ký'];
                foreach ($data as $item) {
                    $statusMap = [
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'attended' => 'Đã tham gia',
                        'rejected' => 'Từ chối',
                    ];
                    $rows[] = [
                        $item->student_code,
                        $item->name,
                        $item->event_title,
                        $item->club_name,
                        $item->club_code,
                        $statusMap[$item->status] ?? $item->status,
                        \Carbon\Carbon::parse($item->registered_at)->format('d/m/Y H:i'),
                    ];
                }
                break;

            case 'violations':
                $headers = ['Loại', 'MSSV/Hoạt động', 'CLB', 'Mã CLB', 'Ngày', 'Mức độ', 'Trạng thái', 'Ghi chú'];
                foreach ($data['activity_violations'] as $item) {
                    $rows[] = [
                        'Hoạt động vi phạm',
                        $item->title,
                        $item->club->name ?? 'N/A',
                        $item->club->code ?? 'N/A',
                        \Carbon\Carbon::parse($item->start_at)->format('d/m/Y'),
                        $item->violation_severity ?? 'N/A',
                        $item->violation_status ?? 'N/A',
                        $item->violation_notes ?? 'N/A',
                    ];
                }
                foreach ($data['student_violations'] as $item) {
                    $rows[] = [
                        'Vi phạm sinh viên',
                        $item->user->student_code ?? 'N/A',
                        $item->club->name ?? 'N/A',
                        $item->club->code ?? 'N/A',
                        \Carbon\Carbon::parse($item->violation_date)->format('d/m/Y'),
                        $item->severity ?? 'N/A',
                        $item->status ?? 'N/A',
                        $item->description ?? 'N/A',
                    ];
                }
                break;
        }

        // Generate CSV content
        $csvContent = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csvContent .= implode(',', $headers) . "\n";

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
     * Export to PDF
     */
    private function exportToPDF($data, $filename, $reportType, $startDate, $endDate)
    {
        // For now, return a simple message. In production, use a PDF library like dompdf or barryvdh/laravel-dompdf
        return response()->json([
            'message' => 'PDF export is not yet implemented. Please use Excel format.',
            'data' => $data
        ], 501);
    }
}

