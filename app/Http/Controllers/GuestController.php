<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\SupportRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * Trang chủ Guest
     */
    public function index()
    {
        // Lấy danh sách CLB nổi bật (active, có nhiều thành viên)
        $featuredClubs = Club::where('status', 'active')
            ->limit(6)
            ->get()
            ->map(function($club) {
                $club->members_count = DB::table('club_members')
                    ->where('club_id', $club->id)
                    ->where('status', 'approved')
                    ->count();
                return $club;
            })
            ->sortByDesc('members_count')
            ->values();

        // Lấy sự kiện sắp diễn ra
        $upcomingEvents = Event::where('start_at', '>=', now())
            ->where('approval_status', 'approved')
            ->orderBy('start_at', 'asc')
            ->limit(6)
            ->with('club:id,name,logo')
            ->get();

        return view('guest.home', compact('featuredClubs', 'upcomingEvents'));
    }

    /**
     * Danh sách tất cả CLB (Guest view)
     */
    public function clubs()
    {
        $query = Club::where('status', 'active');

        // Tìm kiếm theo tên
        if (request()->has('search') && request()->search) {
            $query->where('name', 'like', '%' . request()->search . '%');
        }

        // Lọc theo lĩnh vực
        if (request()->has('field') && request()->field) {
            $query->where('field', request()->field);
        }

        // Đếm số thành viên cho mỗi CLB
        $clubs = $query->get()->map(function($club) {
            $club->members_count = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('status', 'approved')
                ->count();
            return $club;
        });

        // Lấy danh sách lĩnh vực để filter
        $fields = Club::where('status', 'active')
            ->whereNotNull('field')
            ->distinct()
            ->pluck('field');

        return view('guest.clubs', compact('clubs', 'fields'));
    }

    /**
     * Chi tiết CLB (Guest view)
     */
    public function clubDetail($id)
    {
        $club = Club::where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();

        // Đếm số thành viên
        $club->members_count = DB::table('club_members')
            ->where('club_id', $club->id)
            ->where('status', 'approved')
            ->count();

        // Lấy sự kiện gần đây
        $recentEvents = Event::where('club_id', $club->id)
            ->where('approval_status', 'approved')
            ->orderBy('start_at', 'desc')
            ->limit(5)
            ->get();

        // Lấy danh sách thành viên (chỉ hiển thị một số)
        $members = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $club->id)
            ->where('club_members.status', 'approved')
            ->select('users.name', 'users.avatar', 'club_members.position')
            ->limit(10)
            ->get();

        return view('guest.club-detail', compact('club', 'recentEvents', 'members'));
    }

    /**
     * Danh sách sự kiện (Guest view)
     */
    public function events()
    {
        $query = Event::where('approval_status', 'approved')
            ->with('club:id,name,logo');

        // Lọc theo CLB
        if (request()->has('club_id') && request()->club_id) {
            $query->where('club_id', request()->club_id);
        }

        // Lọc theo thời gian
        if (request()->has('time_filter')) {
            $timeFilter = request()->time_filter;
            if ($timeFilter === 'upcoming') {
                $query->where('start_at', '>=', now());
            } elseif ($timeFilter === 'past') {
                $query->where('start_at', '<', now());
            }
        }

        $events = $query->orderBy('start_at', 'desc')->paginate(12);

        // Lấy danh sách CLB để filter
        $clubs = Club::where('status', 'active')
            ->select('id', 'name')
            ->get();

        return view('guest.events', compact('events', 'clubs'));
    }

    /**
     * Trang Giới thiệu
     */
    public function about()
    {
        // Thống kê tổng quan
        $stats = [
            'total_clubs' => Club::where('status', 'active')->count(),
            'total_members' => DB::table('club_members')
                ->where('status', 'approved')
                ->distinct('user_id')
                ->count('user_id'),
            'total_events' => Event::where('approval_status', 'approved')->count(),
        ];

        return view('guest.about', compact('stats'));
    }

    /**
     * Trang Liên hệ
     */
    public function contact()
    {
        return view('guest.contact');
    }

    /**
     * Xử lý gửi liên hệ từ Guest
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'subject.required' => 'Vui lòng nhập tiêu đề',
            'message.required' => 'Vui lòng nhập nội dung',
            'message.min' => 'Nội dung phải có ít nhất 10 ký tự',
        ]);

        SupportRequest::create([
            'sender_type' => 'guest',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'content' => $validated['message'],
            'status' => 'open',
        ]);

        return redirect()->route('guest.contact')
            ->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.');
    }

    /**
     * Trang FAQ
     */
    public function faq()
    {
        return view('guest.faq');
    }

    /**
     * Trang Chính sách bảo mật
     */
    public function privacy()
    {
        return view('guest.privacy');
    }
}
