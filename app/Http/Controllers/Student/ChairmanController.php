<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use App\Models\Event;
use App\Models\Regulation;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class ChairmanController extends Controller
{
    /**
     * Kiểm tra user có phải chủ nhiệm của CLB nào không
     * CHỈ cho phép 1 người: Ưu tiên club_members (position='chairman'), chỉ khi không có mới cho owner_id
     */
    public static function isChairman($userId = null)
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) return null;

        // Ưu tiên kiểm tra từ club_members với position='chairman' trước
        $chairmanClub = DB::table('club_members')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.user_id', $userId)
            ->where('club_members.position', 'chairman')
            ->where('club_members.status', 'approved')
            ->where('clubs.status', 'active')
            ->select('clubs.id', 'clubs.name', 'clubs.code')
            ->first();

        // CHỈ khi KHÔNG có ai trong club_members với position='chairman', mới kiểm tra owner_id
        if (!$chairmanClub) {
            // Lấy tất cả CLB mà user là owner
            $ownerClubs = DB::table('clubs')
                ->where('owner_id', $userId)
                ->where('status', 'active')
                ->select('id', 'name', 'code')
                ->get();
            
            // Với mỗi CLB mà user là owner, kiểm tra xem CLB đó có chủ nhiệm trong club_members chưa
            foreach ($ownerClubs as $club) {
                $hasChairmanInMembers = DB::table('club_members')
                    ->where('club_id', $club->id)
                    ->where('position', 'chairman')
                    ->where('status', 'approved')
                    ->exists();
                
                // Chỉ cho phép owner_id nếu CLB này CHƯA có chủ nhiệm trong club_members
                if (!$hasChairmanInMembers) {
                    $chairmanClub = $club;
                    break; // Chỉ lấy CLB đầu tiên thỏa điều kiện
                }
            }
        }

        return $chairmanClub;
    }

    /**
     * Kiểm tra user có phải chủ nhiệm của một CLB cụ thể không
     * CHỈ cho phép 1 người: Ưu tiên club_members (position='chairman'), chỉ khi không có mới cho owner_id
     */
    public static function isChairmanOfClub($userId, $clubId)
    {
        if (!$userId || !$clubId) return false;

        // Ưu tiên kiểm tra từ club_members với position='chairman' trước
        $isChairmanFromMembers = DB::table('club_members')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.club_id', $clubId)
            ->where('club_members.user_id', $userId)
            ->where('club_members.position', 'chairman')
            ->where('club_members.status', 'approved')
            ->where('clubs.status', 'active')
            ->exists();

        // Nếu user này là chủ nhiệm từ club_members, cho phép
        if ($isChairmanFromMembers) {
            return true;
        }

        // CHỈ khi KHÔNG có ai trong club_members với position='chairman', mới kiểm tra owner_id
        $hasChairmanInMembers = DB::table('club_members')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.club_id', $clubId)
            ->where('club_members.position', 'chairman')
            ->where('club_members.status', 'approved')
            ->where('clubs.status', 'active')
            ->exists();

        // Chỉ cho phép owner_id nếu CLB này CHƯA có chủ nhiệm trong club_members
        if (!$hasChairmanInMembers) {
            $isOwner = DB::table('clubs')
                ->where('id', $clubId)
                ->where('owner_id', $userId)
                ->where('status', 'active')
                ->exists();
            return $isOwner;
        }

        return false;
    }

    /**
     * Giới hạn số lượng cho mỗi chức vụ
     */
    private function getPositionLimit(string $position): ?int
    {
        $limits = [
            'chairman' => 1,
            'vice_chairman' => 2,
            'secretary' => 1,
            'head_expertise' => 1,
            'head_media' => 1,
            'head_events' => 1,
            'treasurer' => 1,
            'member' => null, // Không giới hạn
        ];
        
        return $limits[$position] ?? null;
    }

    /**
     * Kiểm tra số lượng chức vụ có vượt quá giới hạn không
     */
    private function checkPositionLimit(int $clubId, string $position, ?int $excludeMemberId = null): bool
    {
        $limit = $this->getPositionLimit($position);
        
        // Nếu không có giới hạn (member) thì cho phép
        if ($limit === null) {
            return true;
        }

        // Đếm số lượng hiện có (chỉ tính approved)
        $query = DB::table('club_members')
            ->where('club_id', $clubId)
            ->where('position', $position)
            ->where('status', 'approved');
        
        // Nếu đang cập nhật, trừ member hiện tại
        if ($excludeMemberId) {
            $query->where('id', '!=', $excludeMemberId);
        }

        $currentCount = $query->count();
        
        return $currentCount < $limit;
    }

    /**
     * Dashboard Chủ nhiệm CLB
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        // Nếu có club_id trong query parameter, kiểm tra user có phải chủ nhiệm của CLB đó không
        $club_id = $request->query('club_id');
        if ($club_id) {
            $club = Club::findOrFail($club_id);
            
            // Kiểm tra user có phải chủ nhiệm của CLB này không
            $isChairmanOfThisClub = self::isChairmanOfClub($user->id, $club->id);
            
            if (!$isChairmanOfThisClub) {
                return redirect()->route('student.home')
                    ->with('error', 'Bạn không phải chủ nhiệm của CLB này.');
            }
        } else {
            // Nếu không có club_id, lấy CLB đầu tiên mà user là chủ nhiệm
            $chairmanClub = self::isChairman($user->id);
            
            if (!$chairmanClub) {
                return redirect()->route('student.home')
                    ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
            }

            $club = Club::findOrFail($chairmanClub->id);
        }

        // 1. Thông tin CLB cơ bản
        $clubInfo = [
            'name' => $club->name,
            'code' => $club->code,
            'status' => $club->status,
        ];

        // 2. Thống kê nhanh
        $stats = [
            'total_members' => DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('status', 'approved')
                ->count(),
            'pending_registrations' => DB::table('club_registrations')
                ->where('club_id', $club->id)
                ->where('status', 'pending')
                ->count(),
            'upcoming_events' => Event::where('club_id', $club->id)
                ->where('approval_status', 'approved')
                ->whereIn('status', ['upcoming', 'ongoing'])
                ->count(),
            'pending_violations' => DB::table('violations')
                ->where('club_id', $club->id)
                ->where('status', 'pending')
                ->count(),
        ];

        // 2.1. Thống kê chi tiết cho charts
        // Thành viên theo chức vụ
        $membersByPosition = DB::table('club_members')
            ->where('club_id', $club->id)
            ->where('status', 'approved')
            ->select('position', DB::raw('COUNT(*) as count'))
            ->groupBy('position')
            ->get();

        // Thành viên theo giới tính
        $membersByGender = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->where('club_members.status', 'approved')
            ->select('users.gender', DB::raw('COUNT(*) as count'))
            ->groupBy('users.gender')
            ->get();

        // Hoạt động theo trạng thái
        $eventsByStatus = Event::where('club_id', $club->id)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Hoạt động theo tháng (6 tháng gần nhất)
        $eventsByMonth = Event::where('club_id', $club->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // 3. Đơn đăng ký mới (5 đơn gần nhất)
        $newRegistrations = DB::table('club_registrations')
            ->join('users', 'club_registrations.user_id', '=', 'users.id')
            ->where('club_registrations.club_id', $club->id)
            ->where('club_registrations.status', 'pending')
            ->select(
                'club_registrations.id',
                'club_registrations.user_id',
                'club_registrations.created_at',
                'users.name',
                'users.student_code',
                'users.email'
            )
            ->orderBy('club_registrations.created_at', 'desc')
            ->limit(5)
            ->get();

        // 4. Hoạt động sắp diễn ra (5 hoạt động gần nhất)
        $upcomingEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->orderBy('start_at', 'asc')
            ->limit(5)
            ->get();

        // 5. Thông báo & nhắc việc
        $notifications = DB::table('notifications')
            ->where('club_id', $club->id)
            ->orWhere(function($query) use ($club) {
                $query->whereNull('club_id')
                      ->where('type', 'club_alert');
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('student.chairman.dashboard', compact(
            'club',
            'clubInfo',
            'membersByPosition',
            'membersByGender',
            'eventsByStatus',
            'eventsByMonth',
            'stats',
            'newRegistrations',
            'upcomingEvents',
            'notifications'
        ));
    }

    /**
     * Trang quản lý thành viên CLB cho chủ nhiệm
     */
    public function manageMembers(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Nếu có club_id trong query parameter, kiểm tra user có phải chủ nhiệm của CLB đó không
        $club_id = $request->query('club_id');
        if ($club_id) {
            $club = Club::findOrFail($club_id);
            
            // Kiểm tra user có phải chủ nhiệm của CLB này không (cả club_members và owner_id)
            $isChairmanOfThisClub = self::isChairmanOfClub($user->id, $club->id);
            
            if (!$isChairmanOfThisClub) {
                return redirect()->route('student.home')
                    ->with('error', 'Bạn không phải chủ nhiệm của CLB này.');
            }
        } else {
            // Nếu không có club_id, lấy CLB đầu tiên mà user là chủ nhiệm
            $chairmanClub = self::isChairman($user->id);
            if (!$chairmanClub) {
                return redirect()->route('student.home')
                    ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
            }
            $club = Club::findOrFail($chairmanClub->id);
        }
        
        // Lấy danh sách thành viên
        $query = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->select(
                'club_members.id',
                'club_members.user_id',
                'club_members.position',
                'club_members.status',
                'club_members.joined_date',
                'users.name',
                'users.email',
                'users.student_code'
            );
        
        // Tìm kiếm
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('users.name', 'like', '%' . $request->search . '%')
                  ->orWhere('users.student_code', 'like', '%' . $request->search . '%');
            });
        }
        
        // Lọc theo trạng thái
        if ($request->status && $request->status !== 'all') {
            $query->where('club_members.status', $request->status);
        }
        
        // Lọc theo chức vụ
        if ($request->position && $request->position !== 'all') {
            $query->where('club_members.position', $request->position);
        }
        
        $members = $query->orderBy('club_members.position', 'asc')
            ->orderBy('club_members.joined_date', 'desc')
            ->paginate(10);

        // Đếm tổng thành viên
        $memberCount = DB::table('club_members')
            ->where('club_id', $club->id)
            ->where('status', 'approved')
            ->count();

        // Lấy danh sách sinh viên để thêm thành viên
        $students = User::where('role_id', 2)
            ->orderBy('student_code')
            ->select('id', 'name', 'student_code', 'email')
            ->get();

        // Đếm số lượng từng chức vụ (chỉ tính approved) để hiển thị trong form
        $positionCounts = DB::table('club_members')
            ->where('club_id', $club->id)
            ->where('status', 'approved')
            ->select('position', DB::raw('count(*) as count'))
            ->groupBy('position')
            ->pluck('count', 'position')
            ->toArray();

        return view('student.chairman.manage-members', compact('club', 'members', 'memberCount', 'students', 'positionCounts'));
    }

    /**
     * Thêm thành viên vào CLB
     */
    public function addMember(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'position' => 'required|in:chairman,vice_chairman,secretary,head_expertise,head_media,head_events,treasurer,member',
            'status' => 'required|in:pending,approved,rejected,suspended,left',
            'joined_date' => 'nullable|date',
        ]);

        // Kiểm tra thành viên đã tồn tại
        $exists = DB::table('club_members')
            ->where('club_id', $chairmanClub->id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Thành viên này đã tồn tại trong CLB!');
        }

        // Kiểm tra giới hạn số lượng chức vụ
        if (!$this->checkPositionLimit($chairmanClub->id, $request->position)) {
            $limit = $this->getPositionLimit($request->position);
            $positionNames = [
                'chairman' => 'Chủ nhiệm',
                'vice_chairman' => 'Phó Chủ nhiệm',
                'secretary' => 'Thư ký CLB',
                'head_expertise' => 'Trưởng ban Chuyên môn',
                'head_media' => 'Trưởng ban Truyền thông',
                'head_events' => 'Trưởng ban Hoạt động',
                'treasurer' => 'Trưởng ban Tài chính',
            ];
            $positionName = $positionNames[$request->position] ?? $request->position;
            return back()->with('error', "Chức vụ {$positionName} đã đạt giới hạn tối đa ({$limit} người).");
        }

        DB::table('club_members')->insert([
            'club_id' => $chairmanClub->id,
            'user_id' => $request->user_id,
            'position' => $request->position,
            'status' => $request->status,
            'joined_date' => $request->joined_date ?? now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Thêm thành viên thành công!');
    }

    /**
     * Cập nhật thông tin thành viên
     */
    public function updateMember(Request $request, $id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        // Kiểm tra thành viên thuộc CLB của chủ nhiệm
        $member = DB::table('club_members')
            ->where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->first();

        if (!$member) {
            return back()->with('error', 'Thành viên không tồn tại hoặc không thuộc CLB của bạn.');
        }

        $request->validate([
            'position' => 'required|in:chairman,vice_chairman,secretary,head_expertise,head_media,head_events,treasurer,member',
            'status' => 'required|in:pending,approved,rejected,suspended,left',
        ]);

        // Kiểm tra giới hạn số lượng chức vụ (chỉ kiểm tra khi status là approved)
        if ($request->status == 'approved' && $request->position != $member->position) {
            if (!$this->checkPositionLimit($chairmanClub->id, $request->position, $id)) {
                $limit = $this->getPositionLimit($request->position);
                $positionNames = [
                    'chairman' => 'Chủ nhiệm',
                    'vice_chairman' => 'Phó Chủ nhiệm',
                    'secretary' => 'Thư ký CLB',
                    'head_expertise' => 'Trưởng ban Chuyên môn',
                    'head_media' => 'Trưởng ban Truyền thông',
                    'head_events' => 'Trưởng ban Hoạt động',
                    'treasurer' => 'Trưởng ban Tài chính',
                ];
                $positionName = $positionNames[$request->position] ?? $request->position;
                return back()->with('error', "Chức vụ {$positionName} đã đạt giới hạn tối đa ({$limit} người).");
            }
        }

        DB::table('club_members')
            ->where('id', $id)
            ->update([
                'position' => $request->position,
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Cập nhật thành viên thành công!');
    }

    /**
     * Xóa thành viên khỏi CLB
     */
    public function removeMember($id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        // Kiểm tra thành viên thuộc CLB của chủ nhiệm
        $member = DB::table('club_members')
            ->where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->first();

        if (!$member) {
            return back()->with('error', 'Thành viên không tồn tại hoặc không thuộc CLB của bạn.');
        }

        // Không cho xóa chính mình (chủ nhiệm)
        if ($member->user_id == $user->id && $member->position == 'chairman') {
            return back()->with('error', 'Bạn không thể xóa chính mình khỏi CLB.');
        }

        DB::table('club_members')->where('id', $id)->delete();

        return back()->with('success', 'Xóa thành viên thành công!');
    }

    /**
     * Phê duyệt đơn đăng ký
     */
    public function approveMember($id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $member = DB::table('club_members')
            ->where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->first();

        if (!$member) {
            return back()->with('error', 'Thành viên không tồn tại.');
        }

        DB::table('club_members')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Phê duyệt thành viên thành công!');
    }

    /**
     * Từ chối đơn đăng ký
     */
    public function rejectMember($id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $member = DB::table('club_members')
            ->where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->first();

        if (!$member) {
            return back()->with('error', 'Thành viên không tồn tại.');
        }

        DB::table('club_members')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Từ chối đơn đăng ký thành công!');
    }

    /**
     * Đình chỉ thành viên
     */
    public function suspendMember($id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $member = DB::table('club_members')
            ->where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->first();

        if (!$member) {
            return back()->with('error', 'Thành viên không tồn tại.');
        }

        DB::table('club_members')
            ->where('id', $id)
            ->update([
                'status' => 'suspended',
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Đình chỉ thành viên thành công!');
    }

    /**
     * Kích hoạt lại thành viên
     */
    public function activateMember($id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $member = DB::table('club_members')
            ->where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->first();

        if (!$member) {
            return back()->with('error', 'Thành viên không tồn tại.');
        }

        DB::table('club_members')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Kích hoạt thành viên thành công!');
    }

    /**
     * TRANG 1: Quản lý đơn đăng ký vào CLB
     */
    public function manageRegistrations(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Lấy danh sách đơn đăng ký với thông tin về lịch sử tham gia
        $query = DB::table('club_registrations')
            ->join('users', 'club_registrations.user_id', '=', 'users.id')
            ->where('club_registrations.club_id', $club->id)
            ->select(
                'club_registrations.id',
                'club_registrations.user_id',
                'club_registrations.reason',
                'club_registrations.status',
                'club_registrations.created_at',
                'users.name',
                'users.email',
                'users.student_code'
            );

        // Lọc theo trạng thái
        if ($request->status && $request->status !== 'all') {
            $query->where('club_registrations.status', $request->status);
        }

        $registrations = $query->orderBy('club_registrations.created_at', 'desc')
            ->paginate(10);

        // Lấy thông tin về lịch sử tham gia cho mỗi đơn đăng ký
        foreach ($registrations as $reg) {
            // Lấy thông tin từ club_members (nếu có)
            $memberInfo = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('user_id', $reg->user_id)
                ->first();
            
            // Kiểm tra đã từng rời CLB chưa (có trong club_members với status = 'left')
            $hasLeft = $memberInfo && $memberInfo->status === 'left';
            
            // Kiểm tra đang là thành viên (status = 'approved')
            $isCurrentMember = $memberInfo && $memberInfo->status === 'approved';
            
            // Số lần tham gia = join_count từ club_members (nếu có), nếu không thì = 0
            $joinCount = $memberInfo ? ($memberInfo->join_count ?? 1) : 0;
            
            // Nếu đang là thành viên nhưng chưa có join_count, mặc định = 1
            if ($isCurrentMember && $joinCount == 0) {
                $joinCount = 1;
            }
            
            $reg->join_count = $joinCount;
            $reg->has_left = $hasLeft;
            $reg->is_current_member = $isCurrentMember;
            
            // Lấy ngày tham gia từ club_members
            if ($memberInfo) {
                $reg->first_join_date = $memberInfo->joined_date;
                $reg->last_join_date = $memberInfo->joined_date;
            } else {
                $reg->first_join_date = null;
                $reg->last_join_date = null;
            }
        }

        return view('student.chairman.manage-registrations', compact('club', 'registrations'));
    }

    /**
     * Phê duyệt đơn đăng ký vào CLB
     */
    public function approveRegistration($id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $registration = DB::table('club_registrations')
            ->where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->first();

        if (!$registration) {
            return back()->with('error', 'Đơn đăng ký không tồn tại.');
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái đơn đăng ký
            DB::table('club_registrations')
                ->where('id', $id)
                ->update(['status' => 'approved', 'updated_at' => now()]);

            // Kiểm tra xem user đã có trong club_members chưa
            $existingMember = DB::table('club_members')
                ->where('club_id', $chairmanClub->id)
                ->where('user_id', $registration->user_id)
                ->first();

            if ($existingMember) {
                // Nếu đã có, cập nhật status và position, tăng join_count
                $currentJoinCount = $existingMember->join_count ?? 1;
                DB::table('club_members')
                    ->where('id', $existingMember->id)
                    ->update([
                        'position' => 'member',
                        'status' => 'approved',
                        'join_count' => $currentJoinCount + 1, // Tăng số lần tham gia
                        'joined_date' => now()->toDateString(),
                        'updated_at' => now(),
                    ]);
            } else {
                // Nếu chưa có, thêm mới với join_count = 1
                DB::table('club_members')->insert([
                    'club_id' => $chairmanClub->id,
                    'user_id' => $registration->user_id,
                    'position' => 'member',
                    'status' => 'approved',
                    'join_count' => 1, // Lần đầu tham gia
                    'joined_date' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return back()->with('success', 'Phê duyệt đơn đăng ký thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối đơn đăng ký vào CLB
     */
    public function rejectRegistration($id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        DB::table('club_registrations')
            ->where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->update(['status' => 'rejected', 'updated_at' => now()]);

        return back()->with('success', 'Từ chối đơn đăng ký thành công!');
    }

    /**
     * TRANG 2: Gán chức vụ (Phân quyền nội bộ CLB)
     */
    public function managePositions(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Lấy danh sách thành viên đã được phê duyệt với phân trang
        $members = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->where('club_members.status', 'approved')
            ->select(
                'club_members.id',
                'club_members.user_id',
                'club_members.position',
                'users.name',
                'users.student_code',
                'users.email'
            )
            ->orderBy('club_members.position', 'asc')
            ->orderBy('users.name', 'asc')
            ->paginate(10);

        return view('student.chairman.manage-positions', compact('club', 'members'));
    }

    /**
     * Cập nhật chức vụ thành viên
     */
    public function updatePosition(Request $request, $id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $member = DB::table('club_members')
            ->where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->first();

        if (!$member) {
            return back()->with('error', 'Thành viên không tồn tại.');
        }

        // Không cho tự hạ chức của mình
        if ($member->user_id == $user->id) {
            return back()->with('error', 'Bạn không thể thay đổi chức vụ của chính mình.');
        }

        // Không cho chuyển quyền Chủ nhiệm
        if ($request->position == 'chairman') {
            return back()->with('error', 'Chỉ Admin mới được phép gán chức vụ Chủ nhiệm.');
        }

        $request->validate([
            'position' => 'required|in:vice_chairman,secretary,head_expertise,head_media,head_events,treasurer,member',
        ]);

        // Kiểm tra giới hạn số lượng chức vụ
        if (!$this->checkPositionLimit($chairmanClub->id, $request->position, $id)) {
            $limit = $this->getPositionLimit($request->position);
            $positionNames = [
                'vice_chairman' => 'Phó Chủ nhiệm',
                'secretary' => 'Thư ký CLB',
                'head_expertise' => 'Trưởng ban Chuyên môn',
                'head_media' => 'Trưởng ban Truyền thông',
                'head_events' => 'Trưởng ban Hoạt động',
                'treasurer' => 'Trưởng ban Tài chính',
            ];
            $positionName = $positionNames[$request->position] ?? $request->position;
            return back()->with('error', "Chức vụ {$positionName} đã đạt giới hạn tối đa ({$limit} người).");
        }

        DB::table('club_members')
            ->where('id', $id)
            ->update([
                'position' => $request->position,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Cập nhật chức vụ thành công!');
    }

    /**
     * HOẠT ĐỘNG CLB - Tạo hoạt động mới (Chủ nhiệm tạo trực tiếp, không cần duyệt)
     */
    public function createEvent()
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);
        return view('student.chairman.create-event', compact('club'));
    }

    /**
     * HOẠT ĐỘNG CLB - Hoạt động chờ phê duyệt
     */
    public function pendingEvents(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);
        $events = Event::where('club_id', $club->id)
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('student.chairman.pending-events', compact('club', 'events'));
    }

    /**
     * HOẠT ĐỘNG CLB - Danh sách hoạt động đã duyệt
     */
    public function approvedEvents(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Lấy danh sách hoạt động đã được duyệt
        $query = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $events = $query->orderBy('start_at', 'desc')->paginate(10);

        return view('student.chairman.approved-events', compact('club', 'events'));
    }

    /**
     * HOẠT ĐỘNG CLB - Người đăng ký chờ duyệt
     */
    public function pendingRegistrations(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Query cơ bản
        $query = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('events.club_id', $club->id)
            ->where('events.approval_status', '!=', 'rejected'); // Hiển thị tất cả đăng ký trừ hoạt động bị từ chối

        // Lọc theo trạng thái (mặc định là pending nếu không có filter)
        $statusFilter = $request->get('status', 'pending');
        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('event_registrations.status', $statusFilter);
        } else {
            // Nếu chọn "all", hiển thị tất cả
        }

        // Lọc theo hoạt động
        if ($request->event_id) {
            $query->where('events.id', $request->event_id);
        }

        // Tìm kiếm sinh viên (theo tên, MSSV, email)
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.student_code', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $registrations = $query->select(
                'event_registrations.id',
                'event_registrations.event_id',
                'event_registrations.user_id',
                'event_registrations.status',
                'event_registrations.created_at as registration_date',
                'users.name',
                'users.student_code',
                'users.email',
                'events.title as event_title',
                'events.start_at as event_start'
            )
            ->orderBy('event_registrations.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Lấy danh sách events để filter (bao gồm cả pending và approved)
        $events = Event::where('club_id', $club->id)
            ->where('approval_status', '!=', 'rejected')
            ->orderBy('start_at', 'desc')
            ->get();

        return view('student.chairman.pending-registrations', compact('club', 'registrations', 'events', 'statusFilter'));
    }

    /**
     * HOẠT ĐỘNG CLB - Danh sách tham gia đã duyệt
     */
    public function approvedParticipants(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Lấy tất cả đăng ký đã được duyệt của các hoạt động trong CLB
        $query = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('events.club_id', $club->id)
            ->where('events.approval_status', 'approved')
            ->whereIn('event_registrations.status', ['approved', 'attended']);

        // Filter theo event nếu có
        if ($request->event_id) {
            $query->where('events.id', $request->event_id);
        }

        $participants = $query->select(
                'event_registrations.id',
                'event_registrations.event_id',
                'event_registrations.user_id',
                'event_registrations.status',
                'event_registrations.activity_points',
                'event_registrations.created_at as registration_date',
                'users.name',
                'users.student_code',
                'users.email',
                'events.title as event_title',
                'events.start_at as event_start'
            )
            ->orderBy('event_registrations.created_at', 'desc')
            ->paginate(15);

        // Lấy danh sách events để filter
        $events = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->orderBy('start_at', 'desc')
            ->get();

        return view('student.chairman.approved-participants', compact('club', 'participants', 'events'));
    }

    /**
     * Tạo sự kiện mới
     */
    public function storeEvent(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'title' => 'required|string|max:255',
            'activity_type' => 'required|in:academic,arts,volunteer,other',
            'goal' => 'required|string|max:1000',
            'description' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'location' => 'required|string|max:255',
            'expected_participants' => 'nullable|integer|min:1',
            'expected_budget' => 'nullable|numeric|min:0',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB
        ]);

        // Kiểm tra club_id có khớp với CLB của chủ nhiệm không
        if ($request->club_id != $chairmanClub->id) {
            return back()->with('error', 'Bạn không có quyền tạo hoạt động cho CLB này.');
        }

        // Xử lý file đính kèm
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('event_attachments', $fileName, 'public');
        }

        // Chủ nhiệm tạo trực tiếp, không cần duyệt
        Event::create([
            'club_id' => $chairmanClub->id,
            'title' => $request->title,
            'activity_type' => $request->activity_type,
            'goal' => $request->goal,
            'description' => $request->description,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'location' => $request->location,
            'expected_participants' => $request->expected_participants,
            'expected_budget' => $request->expected_budget,
            'attachment' => $attachmentPath,
            'status' => 'upcoming',
            'approval_status' => 'approved', // Chủ nhiệm tạo trực tiếp = approved
            'created_by' => $user->id, // Lưu người tạo
        ]);

        return back()->with('success', 'Tạo hoạt động thành công!');
    }

    /**
     * Cập nhật sự kiện
     */
    public function updateEvent(Request $request, $id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $event = Event::where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:upcoming,ongoing,finished,cancelled',
        ]);

        $event->update($request->only(['title', 'description', 'start_at', 'end_at', 'location', 'status']));

        return back()->with('success', 'Cập nhật sự kiện thành công!');
    }

    /**
     * Xóa sự kiện
     */
    public function deleteEvent($id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $event = Event::where('id', $id)
            ->where('club_id', $chairmanClub->id)
            ->firstOrFail();

        $event->delete();

        return back()->with('success', 'Xóa sự kiện thành công!');
    }

    /**
     * Danh sách người tham gia sự kiện
     */
    public function eventParticipants($eventId)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $event = Event::where('id', $eventId)
            ->where('club_id', $chairmanClub->id)
            ->firstOrFail();

        $participants = DB::table('event_registrations')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('event_registrations.event_id', $eventId)
            ->select(
                'event_registrations.id',
                'event_registrations.user_id',
                'event_registrations.status',
                'event_registrations.activity_points',
                'event_registrations.created_at',
                'users.name',
                'users.student_code',
                'users.email'
            )
            ->orderBy('event_registrations.created_at', 'desc')
            ->get();

        return view('student.chairman.event-participants', compact('event', 'participants'));
    }

    /**
     * Duyệt tham gia sự kiện
     */
    public function approveEventParticipant(Request $request, $registrationId)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $registration = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('event_registrations.id', $registrationId)
            ->where('events.club_id', $chairmanClub->id)
            ->select('event_registrations.*', 'events.title as event_title', 'users.name as user_name', 'users.id as user_id')
            ->first();

        if (!$registration) {
            return back()->with('error', 'Đơn đăng ký không tồn tại.');
        }

        DB::table('event_registrations')
            ->where('id', $registrationId)
            ->update(['status' => 'approved', 'updated_at' => now()]);

        // Tạo thông báo cho sinh viên
        DB::table('notifications')->insert([
            'title' => 'Đăng ký hoạt động được duyệt',
            'body' => "Đơn đăng ký tham gia hoạt động '{$registration->event_title}' của bạn đã được duyệt.",
            'sender_id' => $user->id,
            'is_public' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Lưu thông báo cho user (nếu có bảng user_notifications)
        $notificationId = DB::getPdo()->lastInsertId();
        if (Schema::hasTable('user_notifications')) {
            DB::table('user_notifications')->insert([
                'user_id' => $registration->user_id,
                'notification_id' => $notificationId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Phê duyệt tham gia sự kiện thành công!');
    }

    /**
     * Duyệt hàng loạt
     */
    public function approveBulkRegistrations(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:event_registrations,id',
        ]);

        $registrationIds = $request->registration_ids;
        $approvedCount = 0;
        $failedCount = 0;

        foreach ($registrationIds as $registrationId) {
            $registration = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->where('event_registrations.id', $registrationId)
                ->where('events.club_id', $chairmanClub->id)
                ->where('event_registrations.status', 'pending')
                ->select('event_registrations.*', 'events.title as event_title')
                ->first();

            if (!$registration) {
                $failedCount++;
                continue;
            }

            DB::table('event_registrations')
                ->where('id', $registrationId)
                ->update(['status' => 'approved', 'updated_at' => now()]);

            // Tạo thông báo
            $notificationId = DB::table('notifications')->insertGetId([
                'title' => 'Đăng ký hoạt động được duyệt',
                'body' => "Đơn đăng ký tham gia hoạt động '{$registration->event_title}' của bạn đã được duyệt.",
                'sender_id' => $user->id,
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (Schema::hasTable('user_notifications')) {
                DB::table('user_notifications')->insert([
                    'user_id' => $registration->user_id,
                    'notification_id' => $notificationId,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $approvedCount++;
        }

        $message = "Đã duyệt {$approvedCount} đơn đăng ký.";
        if ($failedCount > 0) {
            $message .= " {$failedCount} đơn không thể duyệt (đã đạt giới hạn hoặc không hợp lệ).";
        }

        return back()->with('success', $message);
    }

    /**
     * Từ chối/Hủy tham gia sự kiện
     */
    public function rejectEventParticipant(Request $request, $registrationId)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        $registration = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('event_registrations.id', $registrationId)
            ->where('events.club_id', $chairmanClub->id)
            ->select('event_registrations.*', 'events.title as event_title', 'users.name as user_name', 'users.id as user_id')
            ->first();

        if (!$registration) {
            return back()->with('error', 'Đơn đăng ký không tồn tại.');
        }

        $reason = $request->reason;

        DB::table('event_registrations')
            ->where('id', $registrationId)
            ->update([
                'status' => 'rejected',
                'notes' => $reason,
                'updated_at' => now()
            ]);

        // Tạo thông báo cho sinh viên
        $notificationId = DB::table('notifications')->insertGetId([
            'title' => 'Đăng ký hoạt động bị từ chối',
            'body' => "Đơn đăng ký tham gia hoạt động '{$registration->event_title}' của bạn đã bị từ chối. Lý do: {$reason}",
            'sender_id' => $user->id,
            'is_public' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (Schema::hasTable('user_notifications')) {
            DB::table('user_notifications')->insert([
                'user_id' => $registration->user_id,
                'notification_id' => $notificationId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Từ chối/Hủy tham gia sự kiện thành công!');
    }

    /**
     * 1. TỔNG QUAN CLB
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Lọc theo năm học (nếu có)
        $academicYear = $request->input('academic_year', now()->year);
        $startYear = $academicYear;
        $endYear = $academicYear + 1;

        // Tổng thành viên
        $totalMembers = DB::table('club_members')
            ->where('club_id', $club->id)
            ->where('status', 'approved')
            ->count();

        // Tổng hoạt động đã tổ chức
        $totalEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->where('status', 'finished')
            ->count();

        // Số hoạt động theo tháng (6 tháng gần nhất)
        $monthlyEvents = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('m/Y');
            $monthName = $date->format('M/Y');
            $count = Event::where('club_id', $club->id)
                ->where('approval_status', 'approved')
                ->whereYear('start_at', $date->year)
                ->whereMonth('start_at', $date->month)
                ->count();
            $monthlyEvents[] = ['month' => $monthName, 'count' => $count];
        }

        // Số hoạt động theo học kỳ
        $semester1Events = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->whereMonth('start_at', '>=', 8)
            ->whereMonth('start_at', '<=', 12)
            ->whereYear('start_at', $startYear)
            ->count();
        $semester2Events = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->where(function($query) use ($startYear, $endYear) {
                $query->where(function($q) use ($endYear) {
                    $q->whereMonth('start_at', '>=', 1)
                      ->whereMonth('start_at', '<=', 5)
                      ->whereYear('start_at', $endYear);
                })->orWhere(function($q) use ($startYear) {
                    $q->whereMonth('start_at', '>=', 1)
                      ->whereMonth('start_at', '<=', 5)
                      ->whereYear('start_at', $startYear + 1);
                });
            })
            ->count();

        // Số hoạt động theo năm học
        $yearEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->where(function($query) use ($startYear, $endYear) {
                $query->whereBetween('start_at', [
                    $startYear . '-08-01',
                    $endYear . '-07-31 23:59:59'
                ]);
            })
            ->count();

        // Tỷ lệ hoạt động đã tổ chức / bị hủy
        $finishedEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->where('status', 'finished')
            ->count();
        $cancelledEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->where('status', 'cancelled')
            ->count();
        $totalApprovedEvents = $finishedEvents + $cancelledEvents;
        $finishedRatio = $totalApprovedEvents > 0 ? round(($finishedEvents / $totalApprovedEvents) * 100) : 0;
        $cancelledRatio = $totalApprovedEvents > 0 ? round(($cancelledEvents / $totalApprovedEvents) * 100) : 0;

        return view('student.chairman.statistics.overview', compact(
            'club',
            'chairmanClub',
            'totalMembers',
            'totalEvents',
            'monthlyEvents',
            'semester1Events',
            'semester2Events',
            'yearEvents',
            'finishedEvents',
            'cancelledEvents',
            'finishedRatio',
            'cancelledRatio',
            'academicYear'
        ));
    }

    /**
     * 2. THỐNG KÊ THÀNH VIÊN CLB
     */
    public function memberStatistics(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Query cơ bản
        $query = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->leftJoin('event_registrations', function($join) use ($club) {
                $join->on('users.id', '=', 'event_registrations.user_id')
                     ->whereIn('event_registrations.status', ['approved', 'attended']);
            })
            ->leftJoin('events', function($join) use ($club) {
                $join->on('event_registrations.event_id', '=', 'events.id')
                     ->where('events.club_id', $club->id)
                     ->where('events.approval_status', 'approved');
            })
            ->where('club_members.club_id', $club->id)
            ->where('club_members.status', 'approved');

        // Lọc theo chức vụ
        if ($request->position && $request->position != 'all') {
            $query->where('club_members.position', $request->position);
        }

        // Tìm kiếm
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.student_code', 'like', "%{$search}%");
            });
        }

        // Thống kê theo nhóm
        $members = $query->select(
                'users.id',
                'users.name',
                'users.student_code',
                'users.email',
                'users.avatar',
                'club_members.position',
                'club_members.created_at as join_date',
                DB::raw('COUNT(DISTINCT events.id) as events_attended'),
                DB::raw('SUM(CASE WHEN event_registrations.status = "attended" THEN 1 ELSE 0 END) as attended_count')
            )
            ->groupBy('users.id', 'users.name', 'users.student_code', 'users.email', 'users.avatar', 'club_members.position', 'club_members.created_at')
            ->orderBy('club_members.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Thống kê tổng hợp
        $statsByPosition = DB::table('club_members')
            ->where('club_id', $club->id)
            ->where('status', 'approved')
            ->select('position', DB::raw('COUNT(*) as count'))
            ->groupBy('position')
            ->get()
            ->pluck('count', 'position');

        // Thành viên tích cực (tham gia >= 3 hoạt động)
        $activeMembers = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->leftJoin('event_registrations', function($join) use ($club) {
                $join->on('users.id', '=', 'event_registrations.user_id')
                     ->where('event_registrations.status', 'attended');
            })
            ->leftJoin('events', function($join) use ($club) {
                $join->on('event_registrations.event_id', '=', 'events.id')
                     ->where('events.club_id', $club->id)
                     ->where('events.approval_status', 'approved');
            })
            ->where('club_members.club_id', $club->id)
            ->where('club_members.status', 'approved')
            ->select(
                'users.id',
                'users.name',
                'users.student_code',
                DB::raw('COUNT(DISTINCT events.id) as events_attended')
            )
            ->groupBy('users.id', 'users.name', 'users.student_code')
            ->havingRaw('COUNT(DISTINCT events.id) >= 3')
            ->orderBy('events_attended', 'desc')
            ->limit(10)
            ->get();

        // Thành viên ít tham gia (< 3 hoạt động hoặc không tham gia)
        $inactiveMembers = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->leftJoin('event_registrations', function($join) use ($club) {
                $join->on('users.id', '=', 'event_registrations.user_id')
                     ->where('event_registrations.status', 'attended');
            })
            ->leftJoin('events', function($join) use ($club) {
                $join->on('event_registrations.event_id', '=', 'events.id')
                     ->where('events.club_id', $club->id)
                     ->where('events.approval_status', 'approved');
            })
            ->where('club_members.club_id', $club->id)
            ->where('club_members.status', 'approved')
            ->select(
                'users.id',
                'users.name',
                'users.student_code',
                DB::raw('COUNT(DISTINCT events.id) as events_attended')
            )
            ->groupBy('users.id', 'users.name', 'users.student_code')
            ->havingRaw('COUNT(DISTINCT events.id) < 3 OR COUNT(DISTINCT events.id) = 0')
            ->orderBy('events_attended', 'asc')
            ->limit(10)
            ->get();

        return view('student.chairman.statistics.members', compact(
            'club',
            'chairmanClub',
            'members',
            'statsByPosition',
            'activeMembers',
            'inactiveMembers'
        ));
    }

    /**
     * 3. THỐNG KÊ HOẠT ĐỘNG CLB
     */
    public function activityStatistics(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Query cơ bản
        $query = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved');

        // Lọc theo trạng thái
        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Lọc theo thời gian
        if ($request->start_date) {
            $query->where('start_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('start_at', '<=', $request->end_date . ' 23:59:59');
        }

        // Tìm kiếm
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->with('club')
            ->withCount(['registrations as participant_count' => function($q) {
                $q->whereIn('status', ['approved', 'attended']);
            }])
            ->orderBy('start_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Thống kê tổng hợp
        $totalEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->count();
        $upcomingEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->where('status', 'upcoming')
            ->count();
        $ongoingEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->where('status', 'ongoing')
            ->count();
        $finishedEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->where('status', 'finished')
            ->count();
        $cancelledEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->where('status', 'cancelled')
            ->count();

        // Biểu đồ hoạt động theo tháng
        $monthlyActivityStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M/Y');
            $count = Event::where('club_id', $club->id)
                ->where('approval_status', 'approved')
                ->whereYear('start_at', $date->year)
                ->whereMonth('start_at', $date->month)
                ->count();
            $monthlyActivityStats[] = ['month' => $month, 'count' => $count];
        }

        return view('student.chairman.statistics.activities', compact(
            'club',
            'chairmanClub',
            'events',
            'totalEvents',
            'upcomingEvents',
            'ongoingEvents',
            'finishedEvents',
            'cancelledEvents',
            'monthlyActivityStats'
        ));
    }

    /**
     * 5. THỐNG KÊ VI PHẠM CLB
     */
    public function violationStatistics(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Query vi phạm hoạt động
        $query = Event::where('club_id', $club->id)
            ->whereNotNull('violation_type')
            ->with('club');

        // Lọc theo mức độ
        if ($request->severity && $request->severity != 'all') {
            $query->where('violation_severity', $request->severity);
        }

        // Lọc theo trạng thái
        if ($request->status && $request->status != 'all') {
            $query->where('violation_status', $request->status);
        }

        // Lọc theo thời gian
        if ($request->start_date) {
            $query->where('violation_detected_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('violation_detected_at', '<=', $request->end_date . ' 23:59:59');
        }

        $violations = $query->orderBy('violation_detected_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Thống kê tổng hợp
        $totalViolations = Event::where('club_id', $club->id)
            ->whereNotNull('violation_type')
            ->count();
        
        $pendingViolations = Event::where('club_id', $club->id)
            ->whereNotNull('violation_type')
            ->whereIn('violation_status', ['pending', 'processing'])
            ->count();
        
        $processedViolations = Event::where('club_id', $club->id)
            ->whereNotNull('violation_type')
            ->where('violation_status', 'processed')
            ->count();

        // Phân loại theo mức độ
        $violationsBySeverity = DB::table('events')
            ->where('club_id', $club->id)
            ->whereNotNull('violation_type')
            ->select('violation_severity', DB::raw('COUNT(*) as count'))
            ->groupBy('violation_severity')
            ->get()
            ->pluck('count', 'violation_severity');

        // Vi phạm theo thành viên (top 10)
        $violationsByMember = DB::table('events')
            ->join('event_registrations', 'events.id', '=', 'event_registrations.event_id')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('events.club_id', $club->id)
            ->whereNotNull('events.violation_type')
            ->select(
                'users.id',
                'users.name',
                'users.student_code',
                DB::raw('COUNT(*) as violation_count')
            )
            ->groupBy('users.id', 'users.name', 'users.student_code')
            ->orderBy('violation_count', 'desc')
            ->limit(10)
            ->get();

        return view('student.chairman.statistics.violations', compact(
            'club',
            'chairmanClub',
            'violations',
            'totalViolations',
            'pendingViolations',
            'processedViolations',
            'violationsBySeverity',
            'violationsByMember'
        ));
    }

    /**
     * TRANG 5: Duyệt hoạt động (Duyệt điểm hoạt động sau khi sự kiện kết thúc)
     */
    public function approveActivities(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Lấy các sự kiện đã kết thúc nhưng chưa được duyệt điểm
        $finishedEvents = DB::table('events')
            ->where('club_id', $club->id)
            ->where('status', 'finished')
            ->orderBy('start_at', 'desc')
            ->get();

        // Lấy danh sách đăng ký tham gia hoạt động cần duyệt điểm
        $pendingApprovals = [];
        foreach ($finishedEvents as $event) {
            $registrations = DB::table('event_registrations')
                ->join('users', 'event_registrations.user_id', '=', 'users.id')
                ->where('event_registrations.event_id', $event->id)
                ->whereIn('event_registrations.status', ['approved', 'attended'])
                ->where(function($query) {
                    $query->whereNull('event_registrations.activity_points')
                          ->orWhere('event_registrations.activity_points', 0);
                })
                ->select(
                    'event_registrations.id',
                    'event_registrations.user_id',
                    'event_registrations.status',
                    'users.name',
                    'users.student_code'
                )
                ->get();

            if ($registrations->count() > 0) {
                $pendingApprovals[] = [
                    'event' => $event,
                    'registrations' => $registrations
                ];
            }
        }

        return view('student.chairman.approve-activities', compact('club', 'pendingApprovals', 'user'));
    }

    /**
     * Duyệt điểm hoạt động cho thành viên
     */
    public function approveActivityPoints(Request $request, $registrationId)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'activity_points' => 'required|integer|min:0|max:100',
        ]);

        $registration = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('event_registrations.id', $registrationId)
            ->where('events.club_id', $chairmanClub->id)
            ->first();

        if (!$registration) {
            return back()->with('error', 'Đơn đăng ký không tồn tại.');
        }

        DB::table('event_registrations')
            ->where('id', $registrationId)
            ->update([
                'activity_points' => $request->activity_points,
                'status' => 'attended',
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Duyệt điểm hoạt động thành công!');
    }

    /**
     * Lịch sử điểm hoạt động (4.2)
     */
    public function activityPointsHistory(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Query cơ bản
        $query = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('events.club_id', $club->id)
            ->where('event_registrations.status', 'attended')
            ->where('event_registrations.activity_points', '>', 0);

        // Lọc theo sinh viên
        if ($request->user_id) {
            $query->where('users.id', $request->user_id);
        }

        // Lọc theo hoạt động
        if ($request->event_id) {
            $query->where('events.id', $request->event_id);
        }

        // Lọc theo khoảng thời gian
        if ($request->start_date) {
            $query->where('event_registrations.updated_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('event_registrations.updated_at', '<=', $request->end_date . ' 23:59:59');
        }

        // Tìm kiếm
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.student_code', 'like', "%{$search}%")
                  ->orWhere('events.title', 'like', "%{$search}%");
            });
        }

        $pointsHistory = $query->select(
                'event_registrations.id',
                'event_registrations.activity_points',
                'event_registrations.updated_at as point_date',
                'users.id as user_id',
                'users.name as user_name',
                'users.student_code',
                'events.id as event_id',
                'events.title as event_title',
                'events.start_at as event_date'
            )
            ->orderBy('event_registrations.updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Lấy danh sách users và events để filter
        $users = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->where('club_members.status', 'approved')
            ->select('users.id', 'users.name', 'users.student_code')
            ->orderBy('users.name', 'asc')
            ->get();

        $events = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->orderBy('start_at', 'desc')
            ->get();

        return view('student.chairman.activity-points-history', compact(
            'club',
            'pointsHistory',
            'users',
            'events'
        ));
    }

    /**
     * Thống kê tham gia (5.1)
     */
    public function participationStatistics(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Lấy thời gian filter (mặc định: 12 tháng gần nhất)
        $startDate = $request->input('start_date', now()->subMonths(12)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Tổng số hoạt động (trong khoảng thời gian)
        $totalEventsQuery = DB::table('events')
            ->where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59']);
        $totalEvents = $totalEventsQuery->count();

        // Tổng lượt tham gia (trong khoảng thời gian, chỉ đã duyệt)
        $totalParticipationsQuery = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.club_id', $club->id)
            ->where('events.approval_status', 'approved')
            ->whereIn('event_registrations.status', ['approved', 'attended'])
            ->whereBetween('event_registrations.created_at', [$startDate, $endDate . ' 23:59:59']);
        $totalParticipations = $totalParticipationsQuery->count();

        // Tổng số sinh viên tham gia (không trùng, trong khoảng thời gian)
        $totalUniqueParticipants = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.club_id', $club->id)
            ->where('events.approval_status', 'approved')
            ->whereIn('event_registrations.status', ['approved', 'attended'])
            ->whereBetween('event_registrations.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->distinct('event_registrations.user_id')
            ->count('event_registrations.user_id');

        // Biểu đồ: Hoạt động theo tháng (trong khoảng thời gian)
        $monthlyEvents = DB::table('events')
            ->where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59'])
            ->select(
                DB::raw('DATE_FORMAT(start_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as event_count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Biểu đồ: Số lượng sinh viên tham gia theo tháng (trong khoảng thời gian)
        $monthlyParticipants = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.club_id', $club->id)
            ->where('events.approval_status', 'approved')
            ->whereIn('event_registrations.status', ['approved', 'attended'])
            ->whereBetween('event_registrations.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->select(
                DB::raw('DATE_FORMAT(event_registrations.created_at, "%Y-%m") as month'),
                DB::raw('COUNT(DISTINCT event_registrations.user_id) as participant_count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Top hoạt động có nhiều người tham gia (trong khoảng thời gian)
        $topEvents = DB::table('events')
            ->leftJoin('event_registrations', function($join) {
                $join->on('events.id', '=', 'event_registrations.event_id')
                     ->whereIn('event_registrations.status', ['approved', 'attended']);
            })
            ->where('events.club_id', $club->id)
            ->where('events.approval_status', 'approved')
            ->whereBetween('events.start_at', [$startDate, $endDate . ' 23:59:59'])
            ->select(
                'events.id',
                'events.title',
                'events.start_at',
                DB::raw('COUNT(DISTINCT event_registrations.user_id) as participant_count')
            )
            ->groupBy('events.id', 'events.title', 'events.start_at')
            ->orderBy('participant_count', 'desc')
            ->limit(10)
            ->get();

        return view('student.chairman.participation-statistics', compact(
            'club',
            'totalEvents',
            'totalParticipations',
            'totalUniqueParticipants',
            'monthlyEvents',
            'monthlyParticipants',
            'topEvents',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Xuất báo cáo (5.2)
     */
    public function exportReport()
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        return view('student.chairman.export-report', compact('club'));
    }

    /**
     * Xử lý xuất báo cáo
     */
    public function generateReport(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'report_type' => 'required|in:overview,members,activities,participations,violations',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:excel,pdf',
        ]);

        $club = Club::findOrFail($chairmanClub->id);
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;
        $reportType = $request->report_type;

        // Tạo dữ liệu báo cáo
        $data = [];
        $filename = '';

        switch ($reportType) {
            case 'overview':
                // Báo cáo tổng quan CLB
                $totalMembers = DB::table('club_members')
                    ->where('club_id', $club->id)
                    ->where('status', 'approved')
                    ->count();
                
                $totalEvents = DB::table('events')
                    ->where('club_id', $club->id)
                    ->where('approval_status', 'approved')
                    ->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59'])
                    ->count();
                
                $totalParticipations = DB::table('event_registrations')
                    ->join('events', 'event_registrations.event_id', '=', 'events.id')
                    ->where('events.club_id', $club->id)
                    ->where('events.approval_status', 'approved')
                    ->whereBetween('events.start_at', [$startDate, $endDate . ' 23:59:59'])
                    ->whereIn('event_registrations.status', ['approved', 'attended'])
                    ->count();
                
                $totalViolations = DB::table('events')
                    ->where('club_id', $club->id)
                    ->whereNotNull('violation_type')
                    ->whereBetween('violation_detected_at', [$startDate, $endDate . ' 23:59:59'])
                    ->count();
                
                $data = [
                    'club' => $club,
                    'total_members' => $totalMembers,
                    'total_events' => $totalEvents,
                    'total_participations' => $totalParticipations,
                    'total_violations' => $totalViolations,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ];
                $filename = 'bao_cao_tong_quan_clb_' . date('Y-m-d') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
                break;

            case 'members':
                // Danh sách thành viên
                $data = DB::table('club_members')
                    ->join('users', 'club_members.user_id', '=', 'users.id')
                    ->leftJoin('event_registrations', function($join) use ($club) {
                        $join->on('users.id', '=', 'event_registrations.user_id')
                             ->whereIn('event_registrations.status', ['approved', 'attended']);
                    })
                    ->leftJoin('events', function($join) use ($club) {
                        $join->on('event_registrations.event_id', '=', 'events.id')
                             ->where('events.club_id', $club->id)
                             ->where('events.approval_status', 'approved');
                    })
                    ->where('club_members.club_id', $club->id)
                    ->where('club_members.status', 'approved')
                    ->whereBetween('club_members.created_at', [$startDate, $endDate . ' 23:59:59'])
                    ->select(
                        'users.id',
                        'users.name',
                        'users.student_code',
                        'users.email',
                        'users.phone',
                        'club_members.position',
                        'club_members.created_at as join_date',
                        DB::raw('COUNT(DISTINCT events.id) as events_attended')
                    )
                    ->groupBy('users.id', 'users.name', 'users.student_code', 'users.email', 'users.phone', 'club_members.position', 'club_members.created_at')
                    ->orderBy('club_members.created_at', 'desc')
                    ->get();
                $filename = 'danh_sach_thanh_vien_' . date('Y-m-d') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
                break;

            case 'activities':
                $data = DB::table('events')
                    ->where('club_id', $club->id)
                    ->where('approval_status', 'approved')
                    ->whereBetween('start_at', [$startDate, $endDate . ' 23:59:59'])
                    ->select(
                        'id',
                        'title',
                        'description',
                        'start_at',
                        'end_at',
                        'location',
                        'status',
                        DB::raw('(SELECT COUNT(DISTINCT user_id) FROM event_registrations WHERE event_id = events.id AND status IN ("approved", "attended")) as participant_count')
                    )
                    ->orderBy('start_at', 'asc')
                    ->get();
                $filename = 'bao_cao_hoat_dong_clb_' . date('Y-m-d') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
                break;

            case 'participations':
                $data = DB::table('event_registrations')
                    ->join('events', 'event_registrations.event_id', '=', 'events.id')
                    ->join('users', 'event_registrations.user_id', '=', 'users.id')
                    ->where('events.club_id', $club->id)
                    ->where('events.approval_status', 'approved') // Chỉ lấy hoạt động đã duyệt
                    ->whereIn('event_registrations.status', ['approved', 'attended', 'rejected']) // Bao gồm cả từ chối để báo cáo đầy đủ
                    ->whereBetween('event_registrations.created_at', [$startDate, $endDate . ' 23:59:59'])
                    ->select(
                        'event_registrations.id',
                        'events.title as event_title',
                        'users.name as user_name',
                        'users.student_code',
                        'users.email',
                        'event_registrations.status',
                        'event_registrations.created_at as registration_date'
                    )
                    ->orderBy('event_registrations.created_at', 'desc')
                    ->get();
                $filename = 'bao_cao_tham_gia_hoat_dong_' . date('Y-m-d') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
                break;

            case 'violations':
                // Báo cáo vi phạm - kỷ luật
                $data = DB::table('events')
                    ->where('club_id', $club->id)
                    ->whereNotNull('violation_type')
                    ->whereBetween('violation_detected_at', [$startDate, $endDate . ' 23:59:59'])
                    ->select(
                        'id',
                        'title',
                        'violation_type',
                        'violation_severity',
                        'violation_status',
                        'violation_notes',
                        'violation_detected_at',
                        'violation_recorded_by'
                    )
                    ->orderBy('violation_detected_at', 'desc')
                    ->get();
                $filename = 'bao_cao_vi_pham_ky_luat_' . date('Y-m-d') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
                break;
        }

        // Xuất file
        if ($format === 'excel') {
            return $this->exportToExcel($data, $filename, $reportType, $club, $startDate, $endDate);
        } else {
            return $this->exportToPDF($data, $filename, $reportType, $club, $startDate, $endDate);
        }
    }

    /**
     * Xuất báo cáo Excel (CSV format)
     */
    private function exportToExcel($data, $filename, $reportType, $club, $startDate, $endDate)
    {
        $headers = [];
        $rows = [];

        switch ($reportType) {
            case 'activities':
                $headers = ['ID', 'Tiêu đề', 'Mô tả', 'Bắt đầu', 'Kết thúc', 'Địa điểm', 'Số người tham gia', 'Trạng thái'];
                foreach ($data as $item) {
                    $rows[] = [
                        $item->id,
                        $item->title,
                        $item->description ?? '',
                        \Carbon\Carbon::parse($item->start_at)->format('d/m/Y H:i'),
                        \Carbon\Carbon::parse($item->end_at)->format('d/m/Y H:i'),
                        $item->location ?? '',
                        $item->participant_count ?? 0,
                        $item->status === 'ongoing' ? 'Đang diễn ra' : ($item->status === 'finished' ? 'Đã kết thúc' : 'Sắp diễn ra')
                    ];
                }
                break;

            case 'members':
                $headers = ['STT', 'Họ tên', 'MSSV', 'Email', 'Số điện thoại', 'Chức vụ', 'Số hoạt động tham gia', 'Ngày tham gia'];
                foreach ($data as $index => $item) {
                    $positionMap = [
                        'chairman' => 'Chủ nhiệm',
                        'vice_chairman' => 'Phó Chủ nhiệm',
                        'secretary' => 'Thư ký',
                        'head_expertise' => 'Trưởng ban Chuyên môn',
                        'head_media' => 'Trưởng ban Truyền thông',
                        'head_events' => 'Trưởng ban Sự kiện',
                        'member' => 'Thành viên',
                    ];
                    $rows[] = [
                        $index + 1,
                        $item->name,
                        $item->student_code ?? '',
                        $item->email ?? '',
                        $item->phone ?? '',
                        $positionMap[$item->position] ?? 'Thành viên',
                        $item->events_attended ?? 0,
                        \Carbon\Carbon::parse($item->join_date)->format('d/m/Y'),
                    ];
                }
                break;

            case 'participations':
                $headers = ['ID', 'Hoạt động', 'Họ tên', 'MSSV', 'Email', 'Trạng thái', 'Ngày đăng ký'];
                foreach ($data as $item) {
                    $statusMap = [
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        'attended' => 'Đã tham gia'
                    ];
                    $rows[] = [
                        $item->id,
                        $item->event_title,
                        $item->user_name,
                        $item->student_code ?? '',
                        $item->email ?? '',
                        $statusMap[$item->status] ?? $item->status,
                        \Carbon\Carbon::parse($item->registration_date)->format('d/m/Y H:i')
                    ];
                }
                break;

            case 'violations':
                $headers = ['ID', 'Tên hoạt động', 'Loại vi phạm', 'Mức độ', 'Trạng thái', 'Ghi chú', 'Ngày phát hiện'];
                foreach ($data as $item) {
                    $severityMap = [
                        'light' => 'Nhẹ',
                        'medium' => 'Trung bình',
                        'serious' => 'Nghiêm trọng'
                    ];
                    $statusMap = [
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'processed' => 'Đã xử lý'
                    ];
                    $rows[] = [
                        $item->id,
                        $item->title,
                        $item->violation_type ?? '',
                        $severityMap[$item->violation_severity] ?? $item->violation_severity,
                        $statusMap[$item->violation_status] ?? $item->violation_status,
                        $item->violation_notes ?? '',
                        $item->violation_detected_at ? \Carbon\Carbon::parse($item->violation_detected_at)->format('d/m/Y H:i') : ''
                    ];
                }
                break;

            case 'points':
                $headers = ['Họ tên', 'MSSV', 'Hoạt động', 'Điểm', 'Ngày ghi nhận'];
                foreach ($data as $item) {
                    $rows[] = [
                        $item->user_name,
                        $item->student_code ?? '',
                        $item->event_title,
                        $item->activity_points,
                        \Carbon\Carbon::parse($item->point_date)->format('d/m/Y H:i')
                    ];
                }
                break;
        }

        // Tạo CSV content
        $csvContent = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csvContent .= "BÁO CÁO - " . strtoupper($club->name) . " (" . $club->code . ")\n";
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
                // Escape commas and quotes
                $cell = str_replace('"', '""', (string)$cell);
                $csvRow[] = '"' . $cell . '"';
            }
            $csvContent .= implode(',', $csvRow) . "\n";
        }

        // Đổi extension thành .csv
        $filename = str_replace('.xlsx', '.csv', $filename);

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Xuất báo cáo PDF
     */
    private function exportToPDF($data, $filename, $reportType, $club, $startDate, $endDate)
    {
        $title = '';
        $headers = [];
        $rows = [];

        switch ($reportType) {
            case 'overview':
                $title = 'Báo cáo tổng quan CLB';
                $headers = ['Thông tin', 'Giá trị'];
                $rows = [
                    ['Tên CLB', $club->name],
                    ['Mã CLB', $club->code],
                    ['Khoảng thời gian', \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y')],
                    ['Tổng thành viên', $data['total_members'] ?? 0],
                    ['Tổng hoạt động', $data['total_events'] ?? 0],
                    ['Tổng lượt tham gia', $data['total_participations'] ?? 0],
                    ['Tổng vi phạm', $data['total_violations'] ?? 0],
                ];
                break;

            case 'members':
                $title = 'Danh sách thành viên';
                $headers = ['STT', 'Họ tên', 'MSSV', 'Email', 'Số điện thoại', 'Chức vụ', 'Số hoạt động tham gia', 'Ngày tham gia'];
                foreach ($data as $index => $item) {
                    $positionMap = [
                        'chairman' => 'Chủ nhiệm',
                        'vice_chairman' => 'Phó Chủ nhiệm',
                        'secretary' => 'Thư ký',
                        'head_expertise' => 'Trưởng ban Chuyên môn',
                        'head_media' => 'Trưởng ban Truyền thông',
                        'head_events' => 'Trưởng ban Sự kiện',
                        'member' => 'Thành viên',
                    ];
                    $rows[] = [
                        $index + 1,
                        $item->name,
                        $item->student_code ?? '',
                        $item->email ?? '',
                        $item->phone ?? '',
                        $positionMap[$item->position] ?? 'Thành viên',
                        $item->events_attended ?? 0,
                        \Carbon\Carbon::parse($item->join_date)->format('d/m/Y'),
                    ];
                }
                break;

            case 'activities':
                $title = 'Báo cáo hoạt động CLB';
                $headers = ['ID', 'Tiêu đề', 'Mô tả', 'Bắt đầu', 'Kết thúc', 'Địa điểm', 'Số người tham gia', 'Trạng thái'];
                foreach ($data as $item) {
                    $rows[] = [
                        $item->id,
                        $item->title,
                        $item->description ?? '',
                        \Carbon\Carbon::parse($item->start_at)->format('d/m/Y H:i'),
                        \Carbon\Carbon::parse($item->end_at)->format('d/m/Y H:i'),
                        $item->location ?? '',
                        $item->participant_count ?? 0,
                        $item->status === 'ongoing' ? 'Đang diễn ra' : ($item->status === 'finished' ? 'Đã kết thúc' : 'Sắp diễn ra')
                    ];
                }
                break;

            case 'participations':
                $title = 'Danh sách tham gia';
                $headers = ['ID', 'Hoạt động', 'Họ tên', 'MSSV', 'Email', 'Trạng thái', 'Ngày đăng ký'];
                foreach ($data as $item) {
                    $statusMap = [
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        'attended' => 'Đã tham gia'
                    ];
                    $rows[] = [
                        $item->id,
                        $item->event_title,
                        $item->user_name,
                        $item->student_code ?? '',
                        $item->email ?? '',
                        $statusMap[$item->status] ?? $item->status,
                        \Carbon\Carbon::parse($item->registration_date)->format('d/m/Y H:i')
                    ];
                }
                break;

            case 'violations':
                $title = 'Báo cáo vi phạm - kỷ luật';
                $headers = ['ID', 'Tên hoạt động', 'Loại vi phạm', 'Mức độ', 'Trạng thái', 'Ghi chú', 'Ngày phát hiện'];
                foreach ($data as $item) {
                    $severityMap = [
                        'light' => 'Nhẹ',
                        'medium' => 'Trung bình',
                        'serious' => 'Nghiêm trọng'
                    ];
                    $statusMap = [
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'processed' => 'Đã xử lý'
                    ];
                    $rows[] = [
                        $item->id,
                        $item->title,
                        $item->violation_type ?? '',
                        $severityMap[$item->violation_severity] ?? $item->violation_severity,
                        $statusMap[$item->violation_status] ?? $item->violation_status,
                        $item->violation_notes ?? '',
                        $item->violation_detected_at ? \Carbon\Carbon::parse($item->violation_detected_at)->format('d/m/Y H:i') : ''
                    ];
                }
                break;
        }

        $html = view('student.chairman.report-pdf', compact('title', 'headers', 'rows', 'club', 'startDate', 'endDate', 'data'))->render();

        $pdf = Pdf::loadHTML($html);
        return $pdf->download($filename);
    }

    /**
     * TRANG 6: Thông tin CLB
     */
    public function clubInfo(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy club_id từ query parameter hoặc lấy CLB đầu tiên mà user là chủ nhiệm
        $club_id = $request->query('club_id');
        $chairmanClub = null;
        if ($club_id) {
            $club = Club::findOrFail($club_id);
            // Kiểm tra user có phải chủ nhiệm của CLB này không (cả club_members và owner_id)
            $isChairmanOfThisClub = self::isChairmanOfClub($user->id, $club->id);
            
            if (!$isChairmanOfThisClub) {
                return redirect()->route('student.home')
                    ->with('error', 'Bạn không phải chủ nhiệm của CLB này.');
            }
            // Tạo $chairmanClub object để truyền vào view
            $chairmanClub = (object)[
                'id' => $club->id,
                'name' => $club->name,
                'code' => $club->code
            ];
        } else {
            $chairmanClub = self::isChairman($user->id);
            if (!$chairmanClub) {
                return redirect()->route('student.home')
                    ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
            }
            $club = Club::findOrFail($chairmanClub->id);
        }

        // B. Ban chủ nhiệm
        $chairman = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->where('club_members.position', 'chairman')
            ->where('club_members.status', 'approved')
            ->select('users.id', 'users.name', 'users.student_code', 'users.email')
            ->first();
        
        // Nếu không có chairman từ club_members, kiểm tra owner_id
        if (!$chairman && $club->owner_id) {
            $owner = DB::table('users')
                ->where('id', $club->owner_id)
                ->select('id', 'name', 'student_code', 'email')
                ->first();
            if ($owner) {
                $chairman = $owner;
            }
        }

        $viceChairmen = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->where('club_members.position', 'vice_chairman')
            ->where('club_members.status', 'approved')
            ->select('club_members.id', 'users.id as user_id', 'users.name', 'users.student_code', 'users.email')
            ->get();

        $executives = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->whereIn('club_members.position', ['secretary', 'head_expertise', 'head_media', 'head_events', 'treasurer'])
            ->where('club_members.status', 'approved')
            ->select('club_members.id', 'club_members.position', 'users.id as user_id', 'users.name', 'users.student_code', 'users.email')
            ->get();

        // C. Thống kê tổng quan
        $totalMembers = DB::table('club_members')
            ->where('club_id', $club->id)
            ->where('status', 'approved')
            ->count();

        $suspendedMembers = DB::table('club_members')
            ->where('club_id', $club->id)
            ->where('status', 'suspended')
            ->count();

        $totalEvents = DB::table('events')
            ->where('club_id', $club->id)
            ->count();

        $pendingRegistrations = DB::table('club_registrations')
            ->where('club_id', $club->id)
            ->where('status', 'pending')
            ->count();

        $avgActivityPoints = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.club_id', $club->id)
            ->where('event_registrations.status', 'attended')
            ->avg('event_registrations.activity_points') ?? 0;

        // E. Hồ sơ pháp lý - Lấy đơn đề nghị thành lập CLB (nếu có)
        // Tìm proposal theo owner_id (người đề xuất ban đầu)
        $clubProposal = null;
        if ($club->owner_id) {
            $clubProposal = DB::table('club_proposals')
                ->where('user_id', $club->owner_id)
                ->where(function($query) use ($club) {
                    $query->where('club_name', $club->name)
                          ->orWhere('club_name', 'like', '%' . $club->name . '%');
                })
                ->orderBy('created_at', 'desc')
                ->first();
        }

        // Danh sách sinh viên để chọn cho ban chủ nhiệm
        $students = User::where('role_id', 2)
            ->orderBy('student_code')
            ->select('id', 'name', 'student_code', 'email')
            ->get();

        return view('student.chairman.club-info', compact(
            'club', 'chairmanClub', 'chairman', 'viceChairmen', 'executives',
            'totalMembers', 'suspendedMembers', 'totalEvents', 'pendingRegistrations', 'avgActivityPoints',
            'clubProposal', 'students'
        ));
    }

    /**
     * Cập nhật thông tin CLB
     */
    public function updateClubInfo(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        $data = $request->validate([
            'club_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'activity_goals' => 'nullable|string',
            'establishment_date' => 'nullable|date',
            'status' => 'required|in:active,pending,archived',
            'banner' => 'nullable|image|max:2048',
            'email' => 'nullable|email|max:255',
            'fanpage' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'social_links' => 'nullable|string',
            'meeting_place' => 'nullable|string|max:255',
            'meeting_schedule' => 'nullable|string|max:255',
            'approval_mode' => 'required|in:auto,manual',
            'activity_approval_mode' => 'required|in:school,chairman',
            'is_public' => 'boolean',
        ]);

        // Xử lý upload logo
        if ($request->hasFile('logo')) {
            if ($club->logo) {
                Storage::disk('public')->delete($club->logo);
            }
            $data['logo'] = $request->file('logo')->store('clubs', 'public');
        }

        // Xử lý is_public (checkbox)
        $data['is_public'] = $request->has('is_public') ? true : false;

        // Chuyển đổi club_type từ tiếng Việt về tiếng Anh để lưu vào database
        if (isset($data['club_type']) && !empty($data['club_type'])) {
            $data['club_type'] = \App\Models\Club::getFieldValue($data['club_type']);
        }

        $club->update($data);

        return back()->with('success', 'Cập nhật thông tin CLB thành công!');
    }

    /**
     * Kiểm tra user có phải chủ nhiệm hoặc phó chủ nhiệm của CLB không
     * CHỈ cho phép 1 chủ nhiệm: Ưu tiên club_members, chỉ khi không có mới cho owner_id
     */
    public static function isChairmanOrVice($userId = null, $clubId = null)
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) return null;

        // Kiểm tra từ club_members với position='chairman' hoặc 'vice_chairman'
        $query = DB::table('club_members')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.user_id', $userId)
            ->whereIn('club_members.position', ['chairman', 'vice_chairman'])
            ->where('club_members.status', 'approved')
            ->where('clubs.status', 'active');

        if ($clubId) {
            $query->where('club_members.club_id', $clubId);
        }

        $result = $query->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                'club_members.position'
            )
            ->first();

        // CHỈ khi KHÔNG tìm thấy từ club_members và user là owner_id, mới kiểm tra owner_id
        // Nhưng chỉ cho phép nếu CLB đó CHƯA có chủ nhiệm trong club_members
        if (!$result) {
            $ownerQuery = DB::table('clubs')
                ->where('owner_id', $userId)
                ->where('status', 'active');
            
            if ($clubId) {
                $ownerQuery->where('id', $clubId);
            }
            
            $ownerClub = $ownerQuery->select('id', 'name', 'code')
                ->first();
            
            if ($ownerClub) {
                // Kiểm tra xem CLB này có chủ nhiệm trong club_members chưa
                $hasChairmanInMembers = DB::table('club_members')
                    ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
                    ->where('club_members.club_id', $ownerClub->id)
                    ->where('club_members.position', 'chairman')
                    ->where('club_members.status', 'approved')
                    ->where('clubs.status', 'active')
                    ->exists();
                
                // Chỉ cho phép owner_id nếu CLB này CHƯA có chủ nhiệm trong club_members
                if (!$hasChairmanInMembers) {
                    // Tạo object giống format của club_members query
                    $result = (object)[
                        'id' => $ownerClub->id,
                        'name' => $ownerClub->name,
                        'code' => $ownerClub->code,
                        'position' => 'chairman'
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Duyệt đề xuất hoạt động - Danh sách đề xuất
     */
    public function approveProposals(Request $request)
    {
        $user = Auth::user();
        
        // Kiểm tra user có phải chủ nhiệm hoặc phó chủ nhiệm không
        $chairmanOrVice = self::isChairmanOrVice($user->id);
        
        if (!$chairmanOrVice) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm hoặc phó chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanOrVice->id);

        // Lấy danh sách đề xuất (chỉ những đề xuất có approval_status = 'pending' và thuộc CLB này)
        $query = DB::table('events')
            ->join('users', 'events.created_by', '=', 'users.id')
            ->leftJoin('club_members', function($join) use ($club) {
                $join->on('users.id', '=', 'club_members.user_id')
                     ->on('club_members.club_id', '=', DB::raw($club->id));
            })
            ->where('events.club_id', $club->id)
            ->where('events.approval_status', 'pending')
            ->select(
                'events.*',
                'users.name as proposer_name',
                'users.student_code as proposer_student_code',
                'users.email as proposer_email',
                'club_members.position as proposer_position'
            );

        // Lọc theo trạng thái (nếu có)
        if ($request->filled('status')) {
            $query->where('events.status', $request->status);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('events.title', 'like', "%{$search}%")
                  ->orWhere('users.name', 'like', "%{$search}%")
                  ->orWhere('users.student_code', 'like', "%{$search}%");
            });
        }

        $proposals = $query->orderBy('events.created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('student.chairman.approve-proposals', compact('club', 'proposals', 'chairmanOrVice'));
    }

    /**
     * Duyệt đề xuất hoạt động
     */
    public function approveProposal(Request $request, $id)
    {
        $user = Auth::user();
        $chairmanOrVice = self::isChairmanOrVice($user->id);
        
        if (!$chairmanOrVice) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $proposal = Event::where('id', $id)
            ->where('club_id', $chairmanOrVice->id)
            ->where('approval_status', 'pending')
            ->firstOrFail();

        // Kiểm tra: Người đề xuất không được tự duyệt
        if ($proposal->created_by == $user->id) {
            return back()->with('error', 'Bạn không thể duyệt đề xuất của chính mình.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'location' => 'required|string|max:255',
        ]);

        // Cập nhật thông tin hoạt động (có thể chỉnh sửa)
        $proposal->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'location' => $request->location,
            'approval_status' => 'approved', // Duyệt đề xuất
            'status' => 'upcoming', // Tạo hoạt động chính thức
            'updated_at' => now(),
        ]);

        // Gửi thông báo cho người đề xuất
        $notificationId = DB::table('notifications')->insertGetId([
            'title' => 'Đề xuất hoạt động được duyệt',
            'body' => "Đề xuất hoạt động '{$proposal->title}' của bạn đã được duyệt bởi " . ($chairmanOrVice->position == 'chairman' ? 'Chủ nhiệm' : 'Phó Chủ nhiệm') . ".",
            'sender_id' => $user->id,
            'is_public' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (Schema::hasTable('user_notifications')) {
            DB::table('user_notifications')->insert([
                'user_id' => $proposal->created_by,
                'notification_id' => $notificationId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Duyệt đề xuất hoạt động thành công!');
    }

    /**
     * Từ chối đề xuất hoạt động
     */
    public function rejectProposal(Request $request, $id)
    {
        $user = Auth::user();
        $chairmanOrVice = self::isChairmanOrVice($user->id);
        
        if (!$chairmanOrVice) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $proposal = Event::where('id', $id)
            ->where('club_id', $chairmanOrVice->id)
            ->where('approval_status', 'pending')
            ->firstOrFail();

        // Kiểm tra: Người đề xuất không được tự từ chối
        if ($proposal->created_by == $user->id) {
            return back()->with('error', 'Bạn không thể từ chối đề xuất của chính mình.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Cập nhật trạng thái từ chối
        $proposal->update([
            'approval_status' => 'rejected',
            'violation_notes' => $request->rejection_reason, // Lưu lý do từ chối
            'updated_at' => now(),
        ]);

        // Gửi thông báo cho người đề xuất
        $notificationId = DB::table('notifications')->insertGetId([
            'title' => 'Đề xuất hoạt động bị từ chối',
            'body' => "Đề xuất hoạt động '{$proposal->title}' của bạn đã bị từ chối. Lý do: {$request->rejection_reason}",
            'sender_id' => $user->id,
            'is_public' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (Schema::hasTable('user_notifications')) {
            DB::table('user_notifications')->insert([
                'user_id' => $proposal->created_by,
                'notification_id' => $notificationId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Từ chối đề xuất hoạt động thành công!');
    }

    /**
     * Danh sách đề xuất hoạt động (tất cả trạng thái)
     */
    public function eventProposals(Request $request)
    {
        $user = Auth::user();
        $chairmanOrVice = self::isChairmanOrVice($user->id);

        if (!$chairmanOrVice) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $club = Club::findOrFail($chairmanOrVice->id);

        // Lấy danh sách tất cả đề xuất (pending, approved, rejected)
        $query = DB::table('events')
            ->join('users', 'events.created_by', '=', 'users.id')
            ->leftJoin('club_members', function($join) use ($club) {
                $join->on('users.id', '=', 'club_members.user_id')
                     ->on('club_members.club_id', '=', DB::raw($club->id));
            })
            ->where('events.club_id', $club->id)
            ->whereNotNull('events.created_by') // Chỉ lấy đề xuất từ sinh viên
            ->select(
                'events.id',
                'events.title',
                'events.activity_type',
                'events.goal',
                'events.description',
                'events.start_at',
                'events.end_at',
                'events.location',
                'events.expected_participants',
                'events.expected_budget',
                'events.attachment',
                'events.approval_status',
                'events.status',
                'events.violation_notes',
                'events.created_at',
                'events.created_by',
                'events.updated_at',
                'users.id as user_id',
                'users.name as proposer_name',
                'users.student_code as proposer_student_code',
                'users.email as proposer_email',
                'club_members.position as proposer_position'
            );

        // Lọc theo trạng thái duyệt
        if ($request->filled('approval_status')) {
            $query->where('events.approval_status', $request->approval_status);
        }

        // Lọc theo loại hoạt động
        if ($request->filled('activity_type')) {
            $query->where('events.activity_type', $request->activity_type);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('events.title', 'like', "%{$search}%")
                  ->orWhere('users.name', 'like', "%{$search}%")
                  ->orWhere('users.student_code', 'like', "%{$search}%");
            });
        }

        $proposals = $query->orderBy('events.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('student.chairman.event-proposals', compact('club', 'proposals', 'user'));
    }

    /**
     * Danh sách nội quy (Chủ nhiệm chỉ xem)
     */
    public function regulations(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Chỉ hiển thị nội quy hệ thống và nội quy của CLB này
        $query = Regulation::with(['club', 'creator', 'updater'])
            ->where(function($q) use ($club) {
                $q->where('scope', 'system')
                  ->orWhere(function($q2) use ($club) {
                      $q2->where('scope', 'club')
                         ->where('club_id', $club->id);
                  });
            })
            ->where('status', 'active');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Lọc theo mức độ
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $regulations = $query->orderBy('issued_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('student.chairman.regulations.index', compact('regulations', 'club', 'chairmanClub'));
    }

    /**
     * Danh sách vi phạm của CLB (Chủ nhiệm xem và quản lý)
     */
    public function violations(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Chỉ hiển thị vi phạm của CLB này
        $query = Violation::with(['user', 'regulation', 'recorder', 'processor'])
            ->where('club_id', $club->id);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('student_code', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo mức độ
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $violations = $query->orderBy('violation_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Lấy danh sách thành viên CLB để chọn khi ghi nhận vi phạm
        $members = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->where('club_members.status', 'approved')
            ->select('users.id', 'users.name', 'users.student_code')
            ->orderBy('users.name')
            ->get();

        // Lấy danh sách nội quy để chọn
        $regulations = Regulation::where(function($q) use ($club) {
                $q->where('scope', 'system')
                  ->orWhere(function($q2) use ($club) {
                      $q2->where('scope', 'club')
                         ->where('club_id', $club->id);
                  });
            })
            ->where('status', 'active')
            ->orderBy('title')
            ->get();

        return view('student.chairman.violations.index', compact('violations', 'club', 'members', 'regulations', 'chairmanClub'));
    }

    /**
     * Form ghi nhận vi phạm mới
     */
    public function createViolation(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Lấy danh sách thành viên CLB
        $members = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->where('club_members.status', 'approved')
            ->select('users.id', 'users.name', 'users.student_code')
            ->orderBy('users.name')
            ->get();

        // Lấy danh sách nội quy
        $regulations = Regulation::where(function($q) use ($club) {
                $q->where('scope', 'system')
                  ->orWhere(function($q2) use ($club) {
                      $q2->where('scope', 'club')
                         ->where('club_id', $club->id);
                  });
            })
            ->where('status', 'active')
            ->orderBy('title')
            ->get();

        return view('student.chairman.violations.create', compact('club', 'members', 'regulations', 'chairmanClub'));
    }

    /**
     * Lưu vi phạm mới
     */
    public function storeViolation(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'regulation_id' => 'required|exists:regulations,id',
            'description' => 'required|string|max:1000',
            'severity' => 'required|in:light,medium,serious',
            'violation_date' => 'required|date',
        ]);

        // Kiểm tra user có phải thành viên của CLB không
        $isMember = DB::table('club_members')
            ->where('club_id', $club->id)
            ->where('user_id', $request->user_id)
            ->where('status', 'approved')
            ->exists();

        if (!$isMember) {
            return back()->withErrors(['user_id' => 'Sinh viên này không phải thành viên của CLB.'])->withInput();
        }

        Violation::create([
            'user_id' => $request->user_id,
            'club_id' => $club->id,
            'regulation_id' => $request->regulation_id,
            'description' => $request->description,
            'severity' => $request->severity,
            'violation_date' => $request->violation_date,
            'recorded_by' => $user->id,
            'status' => 'pending',
        ]);

        return redirect()->route('student.chairman.violations.index')
            ->with('success', 'Ghi nhận vi phạm thành công!');
    }

    /**
     * Xem chi tiết vi phạm
     */
    public function showViolation($id)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        $violation = Violation::with(['user', 'club', 'regulation', 'recorder', 'processor'])
            ->where('id', $id)
            ->where('club_id', $club->id)
            ->firstOrFail();

        return view('student.chairman.violations.show', compact('violation', 'club', 'chairmanClub'));
    }

    /**
     * Lịch sử kỷ luật - Theo thành viên
     */
    public function disciplineHistoryByMember(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Lấy danh sách thành viên CLB
        $members = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->where('club_members.status', 'approved')
            ->select('users.id', 'users.name', 'users.student_code', 'club_members.position')
            ->orderBy('users.name')
            ->get();

        $selectedMemberId = $request->get('member_id');
        $memberViolations = collect();
        $memberStats = null;
        $selectedMember = null;

        if ($selectedMemberId) {
            $selectedMember = $members->firstWhere('id', $selectedMemberId);
            
            if ($selectedMember) {
                // Lấy tất cả vi phạm của thành viên này
                $query = Violation::with(['regulation', 'recorder', 'processor'])
                    ->where('club_id', $club->id)
                    ->where('user_id', $selectedMemberId);

                // Lọc theo mức độ
                if ($request->filled('severity')) {
                    $query->where('severity', $request->severity);
                }

                // Lọc theo trạng thái
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }

                $memberViolations = $query->orderBy('violation_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Thống kê
                $memberStats = [
                    'total' => $memberViolations->count(),
                    'by_severity' => [
                        'light' => $memberViolations->where('severity', 'light')->count(),
                        'medium' => $memberViolations->where('severity', 'medium')->count(),
                        'serious' => $memberViolations->where('severity', 'serious')->count(),
                    ],
                    'by_status' => [
                        'pending' => $memberViolations->where('status', 'pending')->count(),
                        'processed' => $memberViolations->where('status', 'processed')->count(),
                        'monitoring' => $memberViolations->where('status', 'monitoring')->count(),
                    ],
                ];
            }
        }

        return view('student.chairman.discipline-history.by-member', compact(
            'club', 'members', 'selectedMember', 'memberViolations', 'memberStats', 'chairmanClub'
        ));
    }

    /**
     * Lịch sử kỷ luật - Theo thời gian
     */
    public function disciplineHistoryByTime(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = self::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không phải chủ nhiệm của CLB nào.');
        }

        $club = Club::findOrFail($chairmanClub->id);

        // Mặc định lọc 3 tháng gần nhất
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Lấy vi phạm trong khoảng thời gian
        $query = Violation::with(['user', 'regulation', 'recorder', 'processor'])
            ->where('club_id', $club->id)
            ->whereBetween('violation_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Lọc theo mức độ
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $violations = $query->orderBy('violation_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Thống kê
        $allViolations = Violation::where('club_id', $club->id)
            ->whereBetween('violation_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        $stats = [
            'total' => $allViolations->count(),
            'unique_members' => $allViolations->pluck('user_id')->unique()->count(),
            'by_severity' => [
                'light' => $allViolations->where('severity', 'light')->count(),
                'medium' => $allViolations->where('severity', 'medium')->count(),
                'serious' => $allViolations->where('severity', 'serious')->count(),
            ],
            'by_regulation' => $allViolations->groupBy('regulation_id')
                ->map(function($items, $regulationId) {
                    $regulation = \App\Models\Regulation::find($regulationId);
                    return [
                        'regulation' => $regulation,
                        'count' => $items->count(),
                    ];
                })
                ->sortByDesc('count')
                ->take(5)
                ->values(),
        ];

        return view('student.chairman.discipline-history.by-time', compact(
            'club', 'violations', 'stats', 'startDate', 'endDate', 'chairmanClub'
        ));
    }
}

