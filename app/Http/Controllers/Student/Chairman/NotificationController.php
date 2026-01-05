<?php

namespace App\Http\Controllers\Student\Chairman;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Hộp thư thông báo - Chủ nhiệm CLB xem thông báo từ Admin và thông báo nội bộ
     */
    public function inbox(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $clubId = $chairmanClub->id;

        // Lấy thông báo từ Admin (liên quan đến CLB này) và thông báo nội bộ CLB
        $query = Notification::with(['sender', 'club', 'recipients'])
            ->where(function($q) use ($clubId) {
                // Thông báo từ Admin gửi đến CLB này
                $q->where(function($subQ) use ($clubId) {
                    $subQ->where('notification_source', 'admin')
                         ->where('target_type', 'clubs')
                         ->whereJsonContains('target_ids', $clubId);
                })
                // Hoặc thông báo nội bộ CLB (do chủ nhiệm gửi)
                ->orWhere(function($subQ) use ($clubId) {
                    $subQ->where('notification_source', 'club')
                         ->where('club_id', $clubId);
                });
            })
            ->where('status', 'sent');

        // Lọc theo loại thông báo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Lọc theo nguồn (Admin hoặc CLB)
        if ($request->filled('source')) {
            $query->where('notification_source', $request->source);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('start_date')) {
            $query->whereDate('sent_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sent_at', '<=', $request->end_date);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'sent_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $notifications = $query->paginate(10)->withQueryString();

        // Tính số người đã đọc/chưa đọc cho mỗi thông báo
        foreach ($notifications as $notification) {
            $notification->read_count = $notification->readRecipients()->count();
            $notification->unread_count = $notification->unreadRecipients()->count();
            $notification->total_recipients = $notification->recipients()->count();
            
            // Kiểm tra chủ nhiệm đã đọc chưa
            $chairmanRecipient = $notification->recipients()
                ->where('user_id', $user->id)
                ->first();
            $notification->is_read_by_chairman = $chairmanRecipient ? $chairmanRecipient->is_read : false;
        }

        return view('student.chairman.notifications.inbox', compact('notifications', 'chairmanClub'));
    }

    /**
     * Xem chi tiết thông báo
     */
    public function show($id)
    {
        $user = Auth::user();
        $chairmanClub = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $notification = Notification::with([
            'sender',
            'club',
            'recipients.user'
        ])->findOrFail($id);

        // Kiểm tra quyền xem (phải thuộc CLB của chủ nhiệm)
        $clubId = $chairmanClub->id;
        $hasAccess = false;
        
        if ($notification->notification_source === 'admin' && $notification->target_type === 'clubs') {
            $hasAccess = in_array($clubId, $notification->target_ids ?? []);
        } elseif ($notification->notification_source === 'club' && $notification->club_id == $clubId) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            return redirect()->route('student.chairman.notifications.inbox')
                ->with('error', 'Bạn không có quyền xem thông báo này.');
        }

        // Đánh dấu đã đọc
        $recipient = $notification->recipients()->where('user_id', $user->id)->first();
        if ($recipient && !$recipient->is_read) {
            $recipient->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }

        // Tính số người đã đọc/chưa đọc
        $notification->read_count = $notification->readRecipients()->count();
        $notification->unread_count = $notification->unreadRecipients()->count();
        $notification->total_recipients = $notification->recipients()->count();

        return view('student.chairman.notifications.show', compact('notification', 'chairmanClub'));
    }

    /**
     * Form gửi thông báo nội bộ CLB
     */
    public function create()
    {
        $user = Auth::user();
        $chairmanClub = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        return view('student.chairman.notifications.send', compact('chairmanClub'));
    }

    /**
     * Gửi thông báo nội bộ CLB
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:meeting,activity,reminder',
            'scheduled_at' => 'nullable|date|after_or_equal:now',
        ]);

        $clubId = $chairmanClub->id;

        // Xử lý thời gian gửi
        $sendOption = $request->send_option ?? 'now';
        $scheduledAt = null;
        $sendNow = true;

        if ($sendOption === 'scheduled' && $request->filled('scheduled_at')) {
            $scheduledAt = Carbon::parse($request->scheduled_at);
            if ($scheduledAt->isPast()) {
                return back()->with('error', 'Thời gian gửi phải sau thời điểm hiện tại!')->withInput();
            }
            $sendNow = false;
        }

        // Tạo thông báo nội bộ CLB
        $notification = Notification::create([
            'title' => $request->title,
            'body' => $request->body,
            'sender_id' => $user->id,
            'type' => $this->mapClubNotificationType($request->type),
            'target_type' => 'clubs',
            'target_ids' => [$clubId],
            'scheduled_at' => $scheduledAt,
            'status' => $sendNow ? 'sent' : 'scheduled',
            'sent_at' => $sendNow ? now() : null,
            'is_public' => false,
            'notification_source' => 'club',
            'club_id' => $clubId,
        ]);

        // Lấy danh sách thành viên CLB (từ bảng club_members)
        $members = DB::table('club_members')
            ->where('club_id', $clubId)
            ->where('status', 'approved')
            ->pluck('user_id');

        // Tạo bản ghi người nhận cho từng thành viên
        foreach ($members as $memberId) {
            NotificationRecipient::create([
                'notification_id' => $notification->id,
                'user_id' => $memberId,
                'club_id' => null,
                'is_read' => false,
            ]);
        }

        if ($sendNow) {
            return redirect()->route('student.chairman.notifications.inbox')
                ->with('success', 'Thông báo đã được gửi thành công đến ' . count($members) . ' thành viên CLB.');
        } else {
            return redirect()->route('student.chairman.notifications.inbox')
                ->with('success', 'Thông báo đã được lên lịch gửi vào ' . $scheduledAt->format('d/m/Y H:i'));
        }
    }

    /**
     * Map loại thông báo CLB sang loại hệ thống
     */
    private function mapClubNotificationType($clubType)
    {
        $mapping = [
            'meeting' => 'administrative', // Thông báo họp CLB -> Hành chính
            'activity' => 'system', // Thông báo hoạt động -> Hệ thống
            'reminder' => 'administrative', // Nhắc nhở -> Hành chính
        ];
        
        return $mapping[$clubType] ?? 'system';
    }

    /**
     * Lịch sử thông báo nội bộ CLB
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $chairmanClub = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id);
        
        if (!$chairmanClub) {
            return redirect()->route('student.home')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $clubId = $chairmanClub->id;

        // Chỉ lấy thông báo nội bộ CLB (do chủ nhiệm gửi)
        $query = Notification::with(['sender'])
            ->where('notification_source', 'club')
            ->where('club_id', $clubId)
            ->where('status', 'sent');

        // Lọc theo loại thông báo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('start_date')) {
            $query->whereDate('sent_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sent_at', '<=', $request->end_date);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $notifications = $query->orderBy('sent_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Tính số người nhận cho mỗi thông báo
        foreach ($notifications as $notification) {
            $notification->recipient_count = $notification->recipients()->count();
        }

        return view('student.chairman.notifications.history', compact('notifications', 'chairmanClub'));
    }
}
