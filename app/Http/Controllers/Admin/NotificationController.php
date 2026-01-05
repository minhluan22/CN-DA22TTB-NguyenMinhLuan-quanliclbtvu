<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\SupportRequest;
use App\Models\User;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Hộp thư thông báo - Xem tất cả thông báo đã gửi và nhận
     */
    public function inbox(Request $request)
    {
        $query = Notification::with(['sender', 'recipients.user', 'recipients.club'])
            ->where('status', 'sent')
            ->where(function($q) {
                // Hiển thị thông báo từ admin hoặc thông báo hỗ trợ (gửi đến admin)
                $q->where('notification_source', 'admin')
                  ->orWhere('notification_source', 'support');
            });

        // Lọc theo loại thông báo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Lọc theo đối tượng nhận
        if ($request->filled('target_type')) {
            $query->where('target_type', $request->target_type);
        }

        // Lọc theo nguồn thông báo
        if ($request->filled('source')) {
            if ($request->source == 'support') {
                $query->where('notification_source', 'support');
            } elseif ($request->source == 'admin') {
                $query->where('notification_source', 'admin');
            }
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
        }

        return view('admin.notifications.inbox', compact('notifications'));
    }

    /**
     * Xem chi tiết thông báo
     */
    public function show($id)
    {
        $notification = Notification::with([
            'sender',
            'recipients.user',
            'recipients.club'
        ])->findOrFail($id);

        // Tính số người đã đọc/chưa đọc
        $notification->read_count = $notification->readRecipients()->count();
        $notification->unread_count = $notification->unreadRecipients()->count();
        $notification->total_recipients = $notification->recipients()->count();

        // Tìm support request nếu đây là thông báo hỗ trợ
        $supportRequest = null;
        if ($notification->notification_source == 'support' && $notification->sender_id) {
            // Lấy subject từ title của notification
            $titleSubject = $notification->title;
            $titleSubject = str_replace('Yêu cầu hỗ trợ mới: ', '', $titleSubject);
            $titleSubject = str_replace('Yêu cầu hỗ trợ từ Chủ nhiệm: ', '', $titleSubject);
            
            // Tìm support request dựa trên sender_id, subject và thời gian (trong vòng 10 phút)
            $supportRequest = SupportRequest::where('user_id', $notification->sender_id)
                ->where(function($q) use ($titleSubject) {
                    $q->where('subject', 'like', '%' . $titleSubject . '%')
                      ->orWhere('subject', $titleSubject);
                })
                ->whereBetween('created_at', [
                    $notification->created_at->copy()->subMinutes(10),
                    $notification->created_at->copy()->addMinutes(10)
                ])
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Nếu không tìm thấy, tìm support request mới nhất của sender chưa có phản hồi (trong vòng 1 giờ)
            if (!$supportRequest) {
                $supportRequest = SupportRequest::where('user_id', $notification->sender_id)
                    ->whereNull('admin_response')
                    ->whereBetween('created_at', [
                        $notification->created_at->copy()->subHour(),
                        $notification->created_at->copy()->addHour()
                    ])
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
        }

        return view('admin.notifications.show', compact('notification', 'supportRequest'));
    }

    /**
     * Form gửi thông báo
     */
    public function create()
    {
        $clubs = Club::where('status', 'active')->orderBy('name')->get();
        return view('admin.notifications.send', compact('clubs'));
    }

    /**
     * Gửi thông báo
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:system,regulation,administrative',
            'target_type' => 'required|in:all,students,chairmen,clubs',
            'target_ids' => 'required_if:target_type,clubs|array',
            'target_ids.*' => 'exists:clubs,id',
            'send_option' => 'required|in:now,scheduled',
            'scheduled_at' => 'required_if:send_option,scheduled|nullable|date|after_or_equal:now',
        ]);

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

        // Tạo thông báo
        $notification = Notification::create([
            'title' => $request->title,
            'body' => $request->body,
            'sender_id' => Auth::id(),
            'type' => $request->type,
            'target_type' => $request->target_type,
            'target_ids' => $request->target_ids ?? null,
            'scheduled_at' => $scheduledAt,
            'status' => $sendNow ? 'sent' : 'scheduled',
            'sent_at' => $sendNow ? now() : null,
            'is_public' => true,
            'notification_source' => 'admin',
            'club_id' => null,
        ]);

        // Xác định danh sách người nhận
        $recipients = $this->getRecipients($request->target_type, $request->target_ids ?? []);

        // Tạo bản ghi người nhận
        foreach ($recipients as $recipient) {
            NotificationRecipient::create([
                'notification_id' => $notification->id,
                'user_id' => $recipient['user_id'] ?? null,
                'club_id' => $recipient['club_id'] ?? null,
                'is_read' => false,
            ]);
        }

        if ($sendNow) {
            return redirect()->route('admin.notifications.inbox')
                ->with('success', 'Thông báo đã được gửi thành công đến ' . count($recipients) . ' người nhận.');
        } else {
            return redirect()->route('admin.notifications.inbox')
                ->with('success', 'Thông báo đã được lên lịch gửi vào ' . $scheduledAt->format('d/m/Y H:i'));
        }
    }

    /**
     * Lấy danh sách người nhận dựa trên target_type
     */
    private function getRecipients($targetType, $targetIds = [])
    {
        $recipients = [];

        switch ($targetType) {
            case 'all':
                // Tất cả người dùng
                $users = User::where('role_id', '!=', 1)->get(); // Không gửi cho admin
                foreach ($users as $user) {
                    $recipients[] = ['user_id' => $user->id];
                }
                break;

            case 'students':
                // Tất cả sinh viên
                $students = User::where('role_id', 2)->get();
                foreach ($students as $student) {
                    $recipients[] = ['user_id' => $student->id];
                }
                break;

            case 'chairmen':
                // Tất cả chủ nhiệm CLB
                $chairmen = DB::table('club_members')
                    ->where('position', 'chairman')
                    ->where('status', 'approved')
                    ->distinct()
                    ->pluck('user_id');
                
                foreach ($chairmen as $userId) {
                    $recipients[] = ['user_id' => $userId];
                }
                break;

            case 'clubs':
                // CLB cụ thể - gửi cho tất cả thành viên của các CLB này
                if (!empty($targetIds)) {
                    $members = DB::table('club_members')
                        ->whereIn('club_id', $targetIds)
                        ->where('status', 'approved')
                        ->distinct()
                        ->pluck('user_id');
                    
                    foreach ($members as $userId) {
                        $recipients[] = ['user_id' => $userId];
                    }
                }
                break;
        }

        return $recipients;
    }

    /**
     * Lịch sử thông báo
     */
    public function history(Request $request)
    {
        $query = Notification::with(['sender'])
            ->where('status', 'sent');

        // Lọc theo loại thông báo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Lọc theo đối tượng nhận
        if ($request->filled('target_type')) {
            $query->where('target_type', $request->target_type);
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
                  ->orWhere('body', 'like', "%{$search}%")
                  ->orWhereHas('sender', function($senderQuery) use ($search) {
                      $senderQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $notifications = $query->orderBy('sent_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Tính số người nhận cho mỗi thông báo
        foreach ($notifications as $notification) {
            $notification->recipient_count = $notification->recipients()->count();
        }

        return view('admin.notifications.history', compact('notifications'));
    }
}
