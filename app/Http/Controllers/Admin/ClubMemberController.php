<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubMemberController extends Controller
{
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
     * Hiển thị danh sách thành viên CLB
     */
    public function index(Request $request)
    {
        // Lấy tất cả CLB với số lượng thành viên
        $clubs = Club::orderBy('created_at', 'desc')->get();
        
        // Tính số lượng thành viên cho từng CLB
        $memberCounts = DB::table('club_members')
            ->select('club_id', DB::raw('count(*) as total'))
            ->groupBy('club_id')
            ->pluck('total', 'club_id');
        
        $selectedClub = null;
        $members = collect();
        $memberCount = 0;
        
        // Nếu chọn CLB
        if ($request->club_id) {
            $selectedClub = Club::findOrFail($request->club_id);
            
            $query = DB::table('club_members')
                ->join('users', 'club_members.user_id', '=', 'users.id')
                ->where('club_members.club_id', $request->club_id)
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
            
            // Tìm kiếm theo tên
            if ($request->search) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                      ->orWhere('users.student_code', 'like', '%' . $request->search . '%');
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
                            ->paginate(10)
                            ->withQueryString();
            
            // Tổng số thành viên của CLB (tất cả trạng thái)
            $memberCount = $memberCounts[$selectedClub->id] ?? 0;
        }
        
        return view('admin.club-members.index', compact('clubs', 'memberCounts', 'selectedClub', 'members', 'memberCount'));
    }
    
    /**
     * Thêm thành viên vào CLB
     */
    public function store(Request $request)
    {
        $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'user_id' => 'required|exists:users,id',
            'position' => 'required|in:chairman,vice_chairman,secretary,head_expertise,head_media,head_events,treasurer,member',
            'status' => 'required|in:pending,approved,rejected,suspended,left',
            'joined_date' => 'nullable|date',
        ]);
        
        // Kiểm tra thành viên đã tồn tại
        $exists = DB::table('club_members')
            ->where('club_id', $request->club_id)
            ->where('user_id', $request->user_id)
            ->exists();
        
        if ($exists) {
            return back()->with('error', 'Thành viên này đã tồn tại trong CLB!');
        }

        // Kiểm tra giới hạn số lượng chức vụ
        if (!$this->checkPositionLimit($request->club_id, $request->position)) {
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
            'club_id' => $request->club_id,
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'position' => 'required|in:chairman,vice_chairman,secretary,head_expertise,head_media,head_events,treasurer,member',
            'status' => 'required|in:pending,approved,rejected,suspended,left',
        ]);

        // Lấy thông tin member hiện tại để biết club_id
        $member = DB::table('club_members')->where('id', $id)->first();
        if (!$member) {
            return back()->with('error', 'Thành viên không tồn tại!');
        }

        // Kiểm tra giới hạn số lượng chức vụ (chỉ kiểm tra khi status là approved)
        if ($request->status == 'approved' && $request->position != $member->position) {
            if (!$this->checkPositionLimit($member->club_id, $request->position, $id)) {
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
    public function destroy($id)
    {
        DB::table('club_members')->where('id', $id)->delete();
        
        return back()->with('success', 'Xóa thành viên thành công!');
    }
    
    /**
     * Phê duyệt đơn đăng ký
     */
    public function approve($id)
    {
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
    public function reject($id)
    {
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
    public function suspend($id)
    {
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
    public function activate($id)
    {
        DB::table('club_members')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'updated_at' => now(),
            ]);
        
        return back()->with('success', 'Kích hoạt thành viên thành công!');
    }
}
