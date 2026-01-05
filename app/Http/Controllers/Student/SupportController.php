<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use App\Models\Notification;
use App\Models\NotificationRecipient;
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
        return view('student.support.create');
    }

    /**
     * Lưu yêu cầu hỗ trợ
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ], [
            'subject.required' => 'Vui lòng nhập tiêu đề',
            'content.required' => 'Vui lòng nhập nội dung',
            'content.min' => 'Nội dung phải có ít nhất 10 ký tự',
        ]);

        $user = Auth::user();

        $supportRequest = SupportRequest::create([
            'user_id' => $user->id,
            'sender_type' => 'student',
            'student_code' => $user->student_code,
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'status' => 'open',
        ]);

        // Tạo thông báo cho Admin
        $notification = Notification::create([
            'title' => 'Yêu cầu hỗ trợ mới: ' . $validated['subject'],
            'body' => "Sinh viên {$user->name} ({$user->student_code}) đã gửi yêu cầu hỗ trợ: " . \Illuminate\Support\Str::limit($validated['content'], 100),
            'sender_id' => $user->id,
            'type' => 'administrative',
            'target_type' => 'all', // Dùng 'all' vì enum không có 'admins', nhưng chỉ gửi cho admin qua recipients
            'target_ids' => null,
            'status' => 'sent',
            'sent_at' => now(),
            'is_public' => false,
            'notification_source' => 'support',
            'club_id' => null,
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

        return redirect()->route('student.support.index')
            ->with('success', 'Yêu cầu hỗ trợ đã được gửi thành công!');
    }

    /**
     * Danh sách yêu cầu hỗ trợ của sinh viên
     */
    public function index()
    {
        $requests = SupportRequest::where('user_id', Auth::id())
            ->where('sender_type', 'student')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.support.index', compact('requests'));
    }

    /**
     * Chi tiết yêu cầu hỗ trợ
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Kiểm tra user có phải admin không
        $isAdmin = $user->role_id == 1;
        
        // Kiểm tra user có phải chủ nhiệm không
        $isChairman = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id) !== null;
        
        // Nếu là admin hoặc chủ nhiệm, cho phép xem tất cả yêu cầu
        if ($isAdmin || $isChairman) {
            $request = SupportRequest::with(['user', 'club', 'responder'])
                ->findOrFail($id);
        } else {
            // Nếu không phải admin/chủ nhiệm, chỉ cho xem yêu cầu của chính mình
            $request = SupportRequest::where('id', $id)
                ->where('user_id', $user->id)
                ->where('sender_type', 'student')
                ->firstOrFail();
        }

        return view('student.support.show', compact('request', 'isAdmin', 'isChairman'));
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
        $oldStatus = $supportRequest->status;

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

        return redirect()->route('student.support.show', $id)
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
            'club_id' => null,
        ]);

        // Gửi cho user cụ thể
        NotificationRecipient::create([
            'notification_id' => $notification->id,
            'user_id' => $supportRequest->user_id,
            'is_read' => false,
        ]);
    }
}
