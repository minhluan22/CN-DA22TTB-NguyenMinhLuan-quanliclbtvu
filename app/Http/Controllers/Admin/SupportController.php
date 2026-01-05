<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\SupportRequest;
use App\Models\AdminLog;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends BaseAdminController
{
    /**
     * Danh sách liên hệ từ Guest
     */
    public function guestContacts(Request $request)
    {
        $query = SupportRequest::where('sender_type', 'guest');

        // Áp dụng filters và search chung
        $query = $this->applyFilters($query, $request, [
            'status' => ['type' => 'exact', 'column' => 'status'],
        ]);

        $query = $this->applySearch($query, $request, [
            'name',
            'email',
            'subject',
            'content'
        ]);

        $contacts = $this->paginateWithQueryString($query, 15, 'created_at', 'desc');

        return view('admin.support.guest-contacts', compact('contacts'));
    }

    /**
     * Danh sách yêu cầu từ Sinh viên
     */
    public function studentRequests(Request $request)
    {
        $query = SupportRequest::where('sender_type', 'student')
            ->with('user:id,name,email,student_code');

        // Áp dụng filters và search chung
        $query = $this->applyFilters($query, $request, [
            'status' => ['type' => 'exact', 'column' => 'status'],
        ]);

        // Search với relation
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('student_code', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $this->paginateWithQueryString($query, 15, 'created_at', 'desc');

        return view('admin.support.student-requests', compact('requests'));
    }

    /**
     * Danh sách yêu cầu từ Chủ nhiệm CLB
     */
    public function chairmanRequests(Request $request)
    {
        $query = SupportRequest::where('sender_type', 'chairman')
            ->with(['user:id,name,email,student_code', 'club:id,name,code']);

        // Áp dụng filters và search chung
        $query = $this->applyFilters($query, $request, [
            'status' => ['type' => 'exact', 'column' => 'status'],
            'priority' => ['type' => 'exact', 'column' => 'priority'],
        ]);

        // Search với relation
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('club', function($clubQuery) use ($search) {
                      $clubQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // Pagination với multiple order by
        $requests = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.support.chairman-requests', compact('requests'));
    }

    /**
     * Chi tiết yêu cầu hỗ trợ
     */
    public function show($id)
    {
        $request = SupportRequest::with(['user', 'club', 'responder'])
            ->findOrFail($id);

        return view('admin.support.show', compact('request'));
    }

    /**
     * Phản hồi yêu cầu hỗ trợ
     */
    public function respond(Request $request, $id)
    {
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
            'responded_by' => Auth::id(),
            'responded_at' => now(),
        ]);

        // Ghi log
        AdminLog::createLog(
            Auth::id(),
            'update',
            'SupportRequest',
            $supportRequest->id,
            "Phản hồi yêu cầu hỗ trợ từ {$supportRequest->sender_type}",
            ['status' => $oldStatus],
            ['status' => $validated['status'], 'admin_response' => $validated['admin_response']]
        );

        // Gửi thông báo cho người gửi (nếu có user_id)
        if ($supportRequest->user_id) {
            $this->sendNotificationToUser($supportRequest);
        }

        return $this->backWithSuccess('Đã phản hồi yêu cầu hỗ trợ thành công!');
    }

    /**
     * Cập nhật trạng thái
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $supportRequest = SupportRequest::findOrFail($id);
        $oldStatus = $supportRequest->status;

        $supportRequest->update([
            'status' => $validated['status'],
        ]);

        // Ghi log
        AdminLog::createLog(
            Auth::id(),
            'update',
            'SupportRequest',
            $supportRequest->id,
            "Cập nhật trạng thái yêu cầu hỗ trợ",
            ['status' => $oldStatus],
            ['status' => $validated['status']]
        );

        return $this->backWithSuccess('Đã cập nhật trạng thái thành công!');
    }

    /**
     * Đánh dấu đã xử lý (cho guest contacts)
     */
    public function markAsProcessed($id)
    {
        $contact = SupportRequest::findOrFail($id);
        
        $contact->update([
            'status' => 'closed',
            'responded_by' => Auth::id(),
            'responded_at' => now(),
        ]);

        // Ghi log
        AdminLog::createLog(
            Auth::id(),
            'update',
            'SupportRequest',
            $contact->id,
            "Đánh dấu đã xử lý liên hệ từ guest",
            ['status' => $contact->getOriginal('status')],
            ['status' => 'closed']
        );

        return $this->backWithSuccess('Đã đánh dấu đã xử lý!');
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
