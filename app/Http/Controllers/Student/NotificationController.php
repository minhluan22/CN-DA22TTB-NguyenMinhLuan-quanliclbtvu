<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Hộp thư thông báo - Thành viên CLB xem thông báo từ Admin và Chủ nhiệm
     */
    public function inbox(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy danh sách CLB mà user đang tham gia
        $userClubs = DB::table('club_members')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->pluck('club_id')
            ->toArray();

        if (empty($userClubs)) {
            return view('student.notifications.inbox', [
                'notifications' => collect([])->paginate(10),
                'userClubs' => []
            ]);
        }

        // Lấy thông báo từ Admin (gửi đến CLB user tham gia) và thông báo nội bộ CLB
        $query = Notification::with(['sender', 'club'])
            ->where(function($q) use ($userClubs, $user) {
                // Thông báo từ Admin gửi đến CLB user tham gia
                $q->where(function($subQ) use ($userClubs, $user) {
                    $subQ->where('notification_source', 'admin')
                         ->where(function($adminQ) use ($userClubs, $user) {
                             // Nếu is_public = false, chỉ hiển thị cho user có trong recipients
                             $adminQ->where(function($privateQ) use ($user) {
                                 $privateQ->where('is_public', false)
                                          ->whereHas('recipients', function($recipientQ) use ($user) {
                                              $recipientQ->where('user_id', $user->id);
                                          });
                             })
                             // Nếu is_public = true, hiển thị theo target_type
                             ->orWhere(function($publicQ) use ($userClubs) {
                                 $publicQ->where('is_public', true)
                                         ->where(function($targetQ) use ($userClubs) {
                                             // Gửi đến CLB cụ thể
                                             $targetQ->where(function($clubQ) use ($userClubs) {
                                                 foreach ($userClubs as $clubId) {
                                                     $clubQ->orWhereJsonContains('target_ids', $clubId);
                                                 }
                                             })
                                             // Hoặc gửi đến toàn bộ người dùng/sinh viên
                                             ->orWhereIn('target_type', ['all', 'students']);
                                         });
                             });
                         });
                })
                // Hoặc thông báo nội bộ CLB (do chủ nhiệm gửi)
                ->orWhere(function($subQ) use ($userClubs) {
                    $subQ->where('notification_source', 'club')
                         ->whereIn('club_id', $userClubs);
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

        // Lọc theo CLB
        if ($request->filled('club_id')) {
            $clubId = $request->club_id;
            $query->where(function($q) use ($clubId) {
                $q->where(function($subQ) use ($clubId) {
                    $subQ->where('notification_source', 'admin')
                         ->whereJsonContains('target_ids', $clubId);
                })
                ->orWhere(function($subQ) use ($clubId) {
                    $subQ->where('notification_source', 'club')
                         ->where('club_id', $clubId);
                });
            });
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

        // Lấy danh sách CLB để filter
        $clubs = DB::table('clubs')
            ->whereIn('id', $userClubs)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Tính số người đã đọc/chưa đọc và kiểm tra user đã đọc chưa
        foreach ($notifications as $notification) {
            $notification->read_count = $notification->readRecipients()->count();
            $notification->unread_count = $notification->unreadRecipients()->count();
            $notification->total_recipients = $notification->recipients()->count();
            
            // Kiểm tra user đã đọc chưa
            $userRecipient = $notification->recipients()
                ->where('user_id', $user->id)
                ->first();
            $notification->is_read_by_user = $userRecipient ? $userRecipient->is_read : false;
        }

        return view('student.notifications.inbox', compact('notifications', 'clubs', 'userClubs'));
    }

    /**
     * Xem chi tiết thông báo
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $notification = Notification::with([
            'sender',
            'club',
            'recipients.user'
        ])->findOrFail($id);

        // Kiểm tra quyền xem
        $userClubs = DB::table('club_members')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->pluck('club_id')
            ->toArray();

        $hasAccess = false;
        
        if ($notification->notification_source === 'admin') {
            // Thông báo Admin: kiểm tra user có trong danh sách nhận không
            if (in_array('all', [$notification->target_type]) || 
                in_array('students', [$notification->target_type])) {
                $hasAccess = true;
            } elseif ($notification->target_type === 'clubs' && !empty($notification->target_ids)) {
                $hasAccess = !empty(array_intersect($userClubs, $notification->target_ids));
            }
        } elseif ($notification->notification_source === 'club') {
            // Thông báo nội bộ CLB: kiểm tra user có là thành viên CLB không
            $hasAccess = in_array($notification->club_id, $userClubs);
        }

        if (!$hasAccess) {
            return redirect()->route('student.notifications.inbox')
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

        return view('student.notifications.show', compact('notification'));
    }
}
