<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        try {
        // ========== 1. KPI TỔNG QUAN (với so sánh và xu hướng) ==========
        $totalClubs = Club::count();
        $activeClubs = Club::where('status', 'active')->count();
        $inactiveClubs = Club::where('status', '!=', 'active')->count();
        
        // So sánh với tháng trước
        $activeClubsLastMonth = Club::where('status', 'active')
            ->where('created_at', '<', now()->subMonth())
            ->count();
        $activeClubsChange = $activeClubs - $activeClubsLastMonth;
        
        // Tổng thành viên
        $totalMembers = DB::table('club_members')
            ->where('status', 'approved')
            ->count();
        $totalMembersLastMonth = DB::table('club_members')
            ->where('status', 'approved')
            ->where('created_at', '<', now()->subMonth())
            ->count();
        $totalMembersChange = $totalMembers - $totalMembersLastMonth;
        
        // Hoạt động
        $totalEvents = Event::where('approval_status', 'approved')->count();
        $finishedEvents = Event::where('approval_status', 'approved')
            ->where('status', 'finished')
            ->count();
        $finishedEventsLastMonth = Event::where('approval_status', 'approved')
            ->where('status', 'finished')
            ->where('end_at', '<', now()->subMonth())
            ->count();
        $finishedEventsChange = $finishedEvents - $finishedEventsLastMonth;
        
        // Vi phạm đang xử lý (nếu có bảng violations thì dùng, nếu không thì từ events)
        $pendingViolations = 0;
        if (DB::getSchemaBuilder()->hasTable('violations')) {
            $pendingViolations = DB::table('violations')
                ->whereIn('status', ['pending', 'processing'])
                ->count();
        } else {
            // Fallback: đếm từ events nếu có cột violation_type
            if (DB::getSchemaBuilder()->hasColumn('events', 'violation_type')) {
                $pendingViolations = Event::whereNotNull('violation_type')
                    ->count();
            }
        }
        
        // ========== 2. PHÂN TẦNG RỦI RO CLB ==========
        // Kiểm tra xem có cột violation_type không
        $hasViolationType = DB::getSchemaBuilder()->hasColumn('events', 'violation_type');
        $violationSubQuery = $hasViolationType 
            ? '(SELECT club_id, COUNT(*) as violation_count FROM events WHERE violation_type IS NOT NULL GROUP BY club_id)'
            : '(SELECT club_id, 0 as violation_count FROM clubs WHERE 1=0)';
            
        $clubsWithRisk = DB::table('clubs')
            ->leftJoin(DB::raw('(SELECT club_id, COUNT(*) as event_count, MAX(start_at) as last_event FROM events WHERE approval_status = "approved" GROUP BY club_id) as event_stats'), 'clubs.id', '=', 'event_stats.club_id')
            ->leftJoin(DB::raw($violationSubQuery . ' as violation_stats'), 'clubs.id', '=', 'violation_stats.club_id')
            ->leftJoin(DB::raw('(SELECT club_id, COUNT(DISTINCT user_id) as member_count FROM club_members WHERE status = "approved" GROUP BY club_id) as member_stats'), 'clubs.id', '=', 'member_stats.club_id')
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                'clubs.status',
                DB::raw('COALESCE(event_stats.event_count, 0) as event_count'),
                DB::raw('COALESCE(violation_stats.violation_count, 0) as violation_count'),
                DB::raw('COALESCE(member_stats.member_count, 0) as member_count'),
                DB::raw('event_stats.last_event as last_event')
            )
            ->where('clubs.status', 'active')
            ->get()
            ->map(function($club) {
                $riskLevel = 'good';
                $riskReason = '';
                
                // Không có hoạt động > 3 tháng = Nguy cơ
                if ($club->last_event) {
                    $monthsSinceLastEvent = \Carbon\Carbon::parse($club->last_event)->diffInMonths(now());
                    if ($monthsSinceLastEvent > 3) {
                        $riskLevel = 'danger';
                        $riskReason = 'Không có hoạt động ' . $monthsSinceLastEvent . ' tháng';
                    }
                } elseif ($club->event_count == 0) {
                    $riskLevel = 'danger';
                    $riskReason = 'Chưa có hoạt động';
                }
                
                // Nhiều vi phạm = Cảnh báo/Nguy cơ
                if ($club->violation_count > 0) {
                    if ($club->violation_count >= 3) {
                        $riskLevel = 'danger';
                        $riskReason = $riskReason ? $riskReason . '; ' : '';
                        $riskReason .= $club->violation_count . ' vi phạm';
                    } elseif ($club->violation_count >= 2) {
                        if ($riskLevel == 'good') $riskLevel = 'warning';
                        $riskReason = $riskReason ? $riskReason . '; ' : '';
                        $riskReason .= $club->violation_count . ' vi phạm';
                    }
                }
                
                // Ít thành viên = Cảnh báo
                if ($club->member_count < 10) {
                    if ($riskLevel == 'good') $riskLevel = 'warning';
                    $riskReason = $riskReason ? $riskReason . '; ' : '';
                    $riskReason .= 'Chỉ có ' . $club->member_count . ' thành viên';
                }
                
                return [
                    'id' => $club->id,
                    'name' => $club->name,
                    'code' => $club->code,
                    'risk_level' => $riskLevel,
                    'risk_reason' => $riskReason,
                    'event_count' => $club->event_count,
                    'violation_count' => $club->violation_count,
                    'member_count' => $club->member_count,
                ];
            });
        
        $riskSummary = [
            'good' => $clubsWithRisk->where('risk_level', 'good')->count(),
            'warning' => $clubsWithRisk->where('risk_level', 'warning')->count(),
            'danger' => $clubsWithRisk->where('risk_level', 'danger')->count(),
        ];
        
        // Top 5 CLB có nguy cơ
        $topRiskClubs = $clubsWithRisk->where('risk_level', '!=', 'good')
            ->sortByDesc(function($club) {
                return $club['risk_level'] == 'danger' ? 1000 : 100;
            })
            ->take(5)
            ->values();
        
        // ========== 3. CẦN XỬ LÝ NGAY (Action Required) ==========
        $actionRequired = [];
        
        // Hoạt động chờ duyệt (đưa lên đầu)
        $pendingEvents = Event::where('approval_status', 'pending')
            ->with('club')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'club' => $event->club->name ?? 'N/A',
                    'created_at' => $event->created_at->format('d/m/Y'),
                    'url' => route('admin.activities.show', $event->id),
                ];
            });
        
        if ($pendingEvents->count() > 0) {
            $actionRequired[] = [
                'type' => 'info',
                'icon' => '📋',
                'title' => $pendingEvents->count() . ' hoạt động chờ duyệt',
                'items' => $pendingEvents->toArray(),
            ];
        }
        
        // CLB có nhiều vi phạm
        $violationClubs = $clubsWithRisk->where('violation_count', '>=', 2)
            ->sortByDesc('violation_count')
            ->take(5)
            ->values();
        
        if ($violationClubs->count() > 0) {
            $actionRequired[] = [
                'type' => 'warning',
                'icon' => '⚠️',
                'title' => $violationClubs->count() . ' CLB có nhiều vi phạm',
                'items' => $violationClubs->map(function($club) {
                    return [
                        'name' => $club['name'],
                        'code' => $club['code'],
                        'reason' => $club['violation_count'] . ' vi phạm',
                        'url' => route('admin.violations.index', ['club_id' => $club['id']]),
                    ];
                })->toArray(),
            ];
        }
        
        // Hoạt động vi phạm cần xử lý
        $pendingViolationEvents = collect([]);
        if (DB::getSchemaBuilder()->hasTable('violations')) {
            // Kiểm tra xem bảng violations có cột event_id không
            $hasEventId = DB::getSchemaBuilder()->hasColumn('violations', 'event_id');
            if ($hasEventId) {
                try {
                    $pendingViolationEvents = DB::table('violations')
                        ->whereIn('violations.status', ['pending', 'processing'])
                        ->join('events', 'violations.event_id', '=', 'events.id')
                        ->join('clubs', 'events.club_id', '=', 'clubs.id')
                        ->select('violations.*', 'events.title', 'events.id as event_id', 'clubs.name as club_name')
                        ->orderBy('violations.violation_date', 'desc')
                        ->take(5)
                        ->get()
                        ->map(function($item) {
                            return [
                                'id' => $item->event_id,
                                'title' => $item->title ?? 'N/A',
                                'club' => $item->club_name ?? 'N/A',
                                'severity' => $item->severity ?? 'medium',
                                'status' => $item->status ?? 'pending',
                                'url' => route('admin.activities.violations'),
                            ];
                        });
                } catch (\Exception $e) {
                    // Nếu có lỗi, bỏ qua phần này
                    Log::warning('Error fetching pending violations: ' . $e->getMessage());
                }
            }
        }
        
        if ($pendingViolationEvents->count() > 0) {
            $actionRequired[] = [
                'type' => 'danger',
                'icon' => '🚨',
                'title' => $pendingViolationEvents->count() . ' hoạt động vi phạm cần xử lý',
                'items' => $pendingViolationEvents->toArray(),
            ];
        }
        
        
        // ========== 4. BIỂU ĐỒ XU HƯỚNG (6 tháng gần nhất) ==========
        $eventTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('m/Y');
            $monthName = $date->format('M/Y');
            
            $eventCount = Event::where('approval_status', 'approved')
                ->whereYear('start_at', $date->year)
                ->whereMonth('start_at', $date->month)
                ->count();
            
            $eventTrends[] = ['month' => $monthName, 'count' => $eventCount];
        }
        
        // ========== 5. BIỂU ĐỒ PHÂN LOẠI LĨNH VỰC ==========
        // Lấy dữ liệu từ cả field và club_type, ưu tiên field
        $clubFieldStats = DB::table('clubs')
            ->select(
                DB::raw('COALESCE(field, club_type) as field_value'),
                DB::raw('count(*) as count')
            )
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNotNull('field')
                      ->orWhereNotNull('club_type');
            })
            ->groupBy('field_value')
            ->get()
            ->map(function($item) {
                // Chuyển đổi sang tiếng Việt để hiển thị
                $displayValue = \App\Models\Club::getFieldDisplay($item->field_value);
                return [
                    'field' => $displayValue,
                    'field_value' => $item->field_value,
                    'count' => $item->count
                ];
            });
        
        // ========== 6. BIỂU ĐỒ VI PHẠM THEO LOẠI ==========
        $violationByType = collect([]);
        if (DB::getSchemaBuilder()->hasColumn('events', 'violation_type')) {
            $violationByType = DB::table('events')
                ->whereNotNull('violation_type')
                ->select('violation_type', DB::raw('count(*) as count'))
                ->groupBy('violation_type')
                ->orderBy('count', 'desc')
                ->take(5)
                ->get();
        }
        
        // ========== 7. TOP BÁO CÁO NHANH ==========
        // Top 5 CLB hoạt động tốt nhất (nhiều hoạt động, ít vi phạm)
        // Sử dụng lại biến $hasViolationType đã định nghĩa ở trên
        $violationSubQuery2 = $hasViolationType 
            ? '(SELECT club_id, COUNT(*) as violation_count FROM events WHERE violation_type IS NOT NULL GROUP BY club_id)'
            : '(SELECT club_id, 0 as violation_count FROM clubs WHERE 1=0)';
            
        $topActiveClubs = DB::table('clubs')
            ->leftJoin(DB::raw('(SELECT club_id, COUNT(*) as event_count FROM events WHERE approval_status = "approved" AND status = "finished" GROUP BY club_id) as event_stats'), 'clubs.id', '=', 'event_stats.club_id')
            ->leftJoin(DB::raw($violationSubQuery2 . ' as violation_stats'), 'clubs.id', '=', 'violation_stats.club_id')
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                DB::raw('COALESCE(event_stats.event_count, 0) as event_count'),
                DB::raw('COALESCE(violation_stats.violation_count, 0) as violation_count')
            )
            ->where('clubs.status', 'active')
            ->orderBy('event_stats.event_count', 'desc')
            ->orderBy('violation_stats.violation_count', 'asc')
            ->limit(5)
            ->get();
        
        // Đảm bảo tất cả biến đều được định nghĩa
        $data = [
            'totalClubs' => $totalClubs ?? 0,
            'activeClubs' => $activeClubs ?? 0,
            'activeClubsChange' => $activeClubsChange ?? 0,
            'inactiveClubs' => $inactiveClubs ?? 0,
            'totalMembers' => $totalMembers ?? 0,
            'totalMembersChange' => $totalMembersChange ?? 0,
            'totalEvents' => $totalEvents ?? 0,
            'finishedEvents' => $finishedEvents ?? 0,
            'finishedEventsChange' => $finishedEventsChange ?? 0,
            'pendingViolations' => $pendingViolations ?? 0,
            'riskSummary' => $riskSummary ?? ['good' => 0, 'warning' => 0, 'danger' => 0],
            'topRiskClubs' => $topRiskClubs ?? collect([]),
            'actionRequired' => $actionRequired ?? [],
            'eventTrends' => $eventTrends ?? [],
            'clubFieldStats' => $clubFieldStats ?? collect([]),
            'violationByType' => $violationByType ?? collect([]),
            'topActiveClubs' => $topActiveClubs ?? collect([]),
        ];
        
        return view('admin.dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return view('admin.dashboard', [
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage() . ' (File: ' . basename($e->getFile()) . ':' . $e->getLine() . ')',
                'totalClubs' => 0,
                'activeClubs' => 0,
                'activeClubsChange' => 0,
                'inactiveClubs' => 0,
                'totalMembers' => 0,
                'totalMembersChange' => 0,
                'totalEvents' => 0,
                'finishedEvents' => 0,
                'finishedEventsChange' => 0,
                'pendingViolations' => 0,
                'riskSummary' => ['good' => 0, 'warning' => 0, 'danger' => 0],
                'topRiskClubs' => collect([]),
                'actionRequired' => [],
                'eventTrends' => [],
                'clubFieldStats' => collect([]),
                'violationByType' => collect([]),
                'topActiveClubs' => collect([])
            ]);
        }
    }

    /**
     * Upload avatar cho admin
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        }

        try {
            // Xóa avatar cũ nếu có
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Upload avatar mới
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật ảnh đại diện thành công!',
                'avatar_url' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
