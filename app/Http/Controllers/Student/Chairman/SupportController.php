<?php

namespace App\Http\Controllers\Student\Chairman;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    /**
     * Hiển thị form gửi yêu cầu hỗ trợ
     */
    public function create()
    {
        $user = Auth::user();
        
        // Lấy CLB mà user là chủ nhiệm
        $chairmanClub = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id);
        
        // Lấy CLB mà user là chủ nhiệm
        $clubs = Club::where('owner_id', $user->id)
            ->where('status', 'active')
            ->get();

        return view('student.chairman.support.create', compact('clubs', 'chairmanClub'));
    }

    /**
     * Lưu yêu cầu hỗ trợ từ chủ nhiệm
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'priority' => 'nullable|in:low,medium,high',
        ], [
            'club_id.required' => 'Vui lòng chọn CLB',
            'club_id.exists' => 'CLB không tồn tại',
            'subject.required' => 'Vui lòng nhập tiêu đề',
            'content.required' => 'Vui lòng nhập nội dung',
            'content.min' => 'Nội dung phải có ít nhất 10 ký tự',
        ]);

        // Kiểm tra user có phải chủ nhiệm của CLB này không
        $club = Club::where('id', $validated['club_id'])
            ->where('owner_id', Auth::id())
            ->firstOrFail();

        $user = Auth::user();

        $supportRequest = SupportRequest::create([
            'user_id' => $user->id,
            'club_id' => $validated['club_id'],
            'sender_type' => 'chairman',
            'student_code' => $user->student_code,
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'status' => 'open',
            'priority' => $validated['priority'] ?? 'high', // Mặc định ưu tiên cao cho chairman
        ]);

        // Tạo thông báo cho Admin
        $notification = Notification::create([
            'title' => 'Yêu cầu hỗ trợ từ Chủ nhiệm: ' . $validated['subject'],
            'body' => "Chủ nhiệm {$user->name} ({$user->student_code}) của CLB {$club->name} đã gửi yêu cầu hỗ trợ: " . \Illuminate\Support\Str::limit($validated['content'], 100),
            'sender_id' => $user->id,
            'type' => 'administrative',
            'target_type' => 'all', // Dùng 'all' vì enum không có 'admins', nhưng chỉ gửi cho admin qua recipients
            'target_ids' => null,
            'status' => 'sent',
            'sent_at' => now(),
            'is_public' => false,
            'notification_source' => 'support',
            'club_id' => $validated['club_id'],
        ]);

        // Gửi thông báo cho tất cả Admin
        $admins = DB::table('users')
            ->where('role_id', 1)
            ->pluck('id');

        foreach ($admins as $adminId) {
            NotificationRecipient::create([
                'notification_id' => $notification->id,
                'user_id' => $adminId,
                'is_read' => false,
            ]);
        }

        return redirect()->route('student.chairman.support.index')
            ->with('success', 'Yêu cầu hỗ trợ đã được gửi thành công!');
    }

    /**
     * Danh sách yêu cầu hỗ trợ của chủ nhiệm
     */
    public function index()
    {
        $user = Auth::user();
        
        // Lấy CLB mà user là chủ nhiệm
        $chairmanClub = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id);
        
        // Lấy các CLB mà user là chủ nhiệm
        $clubIds = Club::where('owner_id', $user->id)
            ->pluck('id');

        $requests = SupportRequest::where('user_id', $user->id)
            ->where('sender_type', 'chairman')
            ->whereIn('club_id', $clubIds)
            ->with('club:id,name,code')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.chairman.support.index', compact('requests', 'chairmanClub'));
    }

    /**
     * Chi tiết yêu cầu hỗ trợ
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Kiểm tra user có phải admin không
        $isAdmin = $user->role_id == 1;
        
        // Lấy CLB mà user là chủ nhiệm
        $chairmanClub = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id);
        $isChairman = $chairmanClub !== null;
        
        $clubIds = Club::where('owner_id', $user->id)
            ->pluck('id');

        // Nếu là admin, cho phép xem tất cả yêu cầu
        if ($isAdmin) {
            $request = SupportRequest::with(['user', 'club', 'responder'])
                ->findOrFail($id);
        } else {
            // Nếu không phải admin, chỉ cho xem yêu cầu của chính mình
            $request = SupportRequest::where('id', $id)
                ->where('user_id', $user->id)
                ->where('sender_type', 'chairman')
                ->whereIn('club_id', $clubIds)
                ->with(['club', 'responder'])
                ->firstOrFail();
        }

        return view('student.chairman.support.show', compact('request', 'chairmanClub', 'isAdmin', 'isChairman'));
    }

    /**
     * Phản hồi yêu cầu hỗ trợ (cho admin/chủ nhiệm)
     */
    public function respond(Request $request, $id)
    {
        $user = Auth::user();
        
        // Kiểm tra quyền
        $isAdmin = $user->role_id == 1;
        $isChairman = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id) !== null;
        
        if (!$isAdmin && !$isChairman) {
            return back()->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }

        $validated = $request->validate([
            'admin_response' => 'required|string|min:10',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ], [
            'admin_response.required' => 'Vui lòng nhập phản hồi',
            'admin_response.min' => 'Phản hồi phải có ít nhất 10 ký tự',
            'status.required' => 'Vui lòng chọn trạng thái',
        ]);

        $supportRequest = SupportRequest::findOrFail($id);

        $supportRequest->update([
            'admin_response' => $validated['admin_response'],
            'status' => $validated['status'],
            'responded_by' => $user->id,
            'responded_at' => now(),
        ]);

        // Gửi thông báo cho người gửi (nếu có user_id)
        if ($supportRequest->user_id) {
            $this->sendNotificationToUser($supportRequest);
        }

        return redirect()->route('student.chairman.support.show', $id)
            ->with('success', 'Đã phản hồi yêu cầu hỗ trợ thành công!');
    }

    /**
     * Gửi thông báo cho người dùng
     */
    private function sendNotificationToUser(SupportRequest $supportRequest)
    {
        $notification = Notification::create([
            'title' => 'Phản hồi yêu cầu hỗ trợ',
            'body' => "Yêu cầu hỗ trợ của bạn đã được phản hồi: {$supportRequest->subject}",
            'sender_id' => Auth::id(),
            'type' => 'administrative',
            'target_type' => 'students', // Dùng 'students' nhưng chỉ gửi cho user cụ thể
            'status' => 'sent',
            'sent_at' => now(),
            'is_public' => false, // Quan trọng: false để chỉ user nhận mới thấy
            'notification_source' => 'admin',
            'club_id' => $supportRequest->club_id,
        ]);

        // Gửi cho user cụ thể
        NotificationRecipient::create([
            'notification_id' => $notification->id,
            'user_id' => $supportRequest->user_id,
            'is_read' => false,
        ]);
    }
}
