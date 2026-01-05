<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Student\ChairmanController;
use App\Models\Club;
use App\Models\Event;
use App\Models\ClubProposal;
use App\Models\Notification;

class StudentController extends Controller
{
    public function home()
    {
        $user = Auth::user();

        // Không tự động redirect nữa - cho phép chủ nhiệm xem trang chủ SV bình thường
        // Chủ nhiệm có thể truy cập trang chủ SV qua menu sidebar

        $memberships = collect();
        $stats = [
            'joined' => 0,
            'pending' => 0,
            'registered' => 0,
            'upcoming' => 0,
            'activity_points' => 0,
            'events_attended' => 0,
        ];

        $recentNotifications = collect();
        $upcomingEvents = collect();
        $recentActivities = collect();
        $userClubs = [];

        if ($user) {
            // Lấy danh sách CLB mà user đang tham gia (từ club_members)
            $userClubsFromMembers = DB::table('club_members')
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->pluck('club_id')
                ->toArray();

            // Lấy danh sách CLB mà user là chủ nhiệm (từ owner_id) - CHỈ khi CLB đó CHƯA có chủ nhiệm trong club_members
            $userClubsAsOwner = DB::table('clubs')
                ->where('owner_id', $user->id)
                ->where('status', 'active')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('club_members')
                        ->whereColumn('club_members.club_id', 'clubs.id')
                        ->where('club_members.position', 'chairman')
                        ->where('club_members.status', 'approved');
                })
                ->pluck('id')
                ->toArray();

            // Merge và loại bỏ trùng lặp
            $userClubs = array_unique(array_merge($userClubsFromMembers, $userClubsAsOwner));

            // Lấy thông tin membership từ club_members
            $memberships = DB::table('club_members')
                ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
                ->select(
                    'club_members.*',
                    'clubs.name as club_name',
                    'clubs.code as club_code',
                    'clubs.field as club_field',
                    'clubs.club_type',
                    'clubs.status as club_status',
                    'clubs.logo as club_logo'
                )
                ->where('club_members.user_id', $user->id)
                ->orderByRaw("
                    CASE 
                        WHEN club_members.position = 'chairman' THEN 1
                        WHEN club_members.position = 'vice_chairman' THEN 2
                        ELSE 3
                    END
                ")
                ->orderBy('club_members.created_at', 'desc')
                ->get();

            // Thêm CLB mà user là chủ nhiệm (owner_id) - CHỈ khi CLB đó CHƯA có chủ nhiệm trong club_members
            $clubsAsOwner = DB::table('clubs')
                ->where('owner_id', $user->id)
                ->where('status', 'active')
                ->whereNotIn('id', $memberships->pluck('club_id')->toArray())
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('club_members')
                        ->whereColumn('club_members.club_id', 'clubs.id')
                        ->where('club_members.position', 'chairman')
                        ->where('club_members.status', 'approved');
                })
                ->get()
                ->map(function($club) {
                    return (object)[
                        'club_id' => $club->id,
                        'user_id' => $club->owner_id,
                        'position' => 'chairman',
                        'status' => 'approved',
                        'club_name' => $club->name,
                        'club_code' => $club->code,
                        'club_field' => $club->field,
                        'club_type' => $club->club_type,
                        'club_status' => $club->status,
                        'club_logo' => $club->logo,
                        'joined_date' => $club->created_at ? \Carbon\Carbon::parse($club->created_at)->toDateString() : now()->toDateString(),
                        'created_at' => $club->created_at,
                    ];
                });

            // Merge vào memberships
            $memberships = $memberships->merge($clubsAsOwner)->sortBy(function($item) {
                $priority = ['chairman' => 1, 'vice_chairman' => 2];
                return $priority[$item->position ?? 'member'] ?? 3;
            })->values();

            $stats['joined'] = $memberships->where('status', 'approved')->count();
            $stats['pending'] = $memberships->where('status', 'pending')->count();

            // Đếm số sự kiện đã đăng ký
            $stats['registered'] = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->where('event_registrations.user_id', $user->id)
                ->whereIn('event_registrations.status', ['pending', 'approved'])
                ->where('events.start_at', '>', now())
                ->count();

            // Đếm số sự kiện sắp diễn ra
            $stats['upcoming'] = DB::table('events')
                ->whereIn('club_id', $userClubs)
                ->where('status', 'upcoming')
                ->where('start_at', '>', now())
                ->where('start_at', '<=', now()->addDays(30))
                ->count();

            // Tổng điểm hoạt động
            $stats['activity_points'] = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->where('event_registrations.user_id', $user->id)
                ->where('event_registrations.status', 'attended')
                ->sum('event_registrations.activity_points') ?? 0;

            // Số sự kiện đã tham gia
            $stats['events_attended'] = DB::table('event_registrations')
                ->where('user_id', $user->id)
                ->where('status', 'attended')
                ->count();

            // Lấy thông báo mới nhất (5 thông báo chưa đọc)
            if (!empty($userClubs)) {
                $recentNotifications = Notification::with(['sender', 'club'])
                    ->where('status', 'sent')
                    ->where(function($q) use ($userClubs, $user) {
                        // Thông báo từ Admin
                        $q->where(function($adminQ) use ($userClubs, $user) {
                            $adminQ->where('notification_source', 'admin')
                                 ->where(function($targetQ) use ($userClubs, $user) {
                                     // Nếu is_public = false, chỉ hiển thị cho user có trong recipients
                                     $targetQ->where(function($privateQ) use ($user) {
                                         $privateQ->where('is_public', false)
                                                  ->whereHas('recipients', function($recipientQ) use ($user) {
                                                      $recipientQ->where('user_id', $user->id)
                                                                 ->where('is_read', false);
                                                  });
                                     })
                                     // Nếu is_public = true, hiển thị theo target_type
                                     ->orWhere(function($publicQ) use ($userClubs, $user) {
                                         $publicQ->where('is_public', true)
                                                 ->where(function($typeQ) use ($userClubs) {
                                                     // Gửi đến CLB cụ thể
                                                     foreach ($userClubs as $clubId) {
                                                         $typeQ->orWhereJsonContains('target_ids', $clubId);
                                                     }
                                                     // Hoặc gửi đến toàn bộ người dùng/sinh viên
                                                     $typeQ->orWhereIn('target_type', ['all', 'students']);
                                                 })
                                                 ->whereHas('recipients', function($recipientQ) use ($user) {
                                                     $recipientQ->where('user_id', $user->id)
                                                                ->where('is_read', false);
                                                 });
                                     });
                                 });
                        })
                        // Hoặc thông báo nội bộ CLB
                        ->orWhere(function($clubQ) use ($userClubs, $user) {
                            $clubQ->where('notification_source', 'club')
                                  ->whereIn('club_id', $userClubs)
                                  ->whereHas('recipients', function($recipientQ) use ($user) {
                                      $recipientQ->where('user_id', $user->id)
                                                 ->where('is_read', false);
                                  });
                        });
                    })
                    ->orderBy('sent_at', 'desc')
                    ->limit(5)
                    ->get();
            } else {
                // Nếu chưa tham gia CLB, chỉ lấy thông báo Admin gửi toàn hệ thống
                $recentNotifications = Notification::with(['sender'])
                    ->where('status', 'sent')
                    ->where('notification_source', 'admin')
                    ->where(function($q) use ($user) {
                        // Nếu is_public = false, chỉ hiển thị cho user có trong recipients
                        $q->where(function($privateQ) use ($user) {
                            $privateQ->where('is_public', false)
                                     ->whereHas('recipients', function($recipientQ) use ($user) {
                                         $recipientQ->where('user_id', $user->id)
                                                    ->where('is_read', false);
                                     });
                        })
                        // Nếu is_public = true, hiển thị theo target_type
                        ->orWhere(function($publicQ) {
                            $publicQ->where('is_public', true)
                                    ->whereIn('target_type', ['all', 'students'])
                                    ->whereHas('recipients', function($recipientQ) use ($user) {
                                        $recipientQ->where('user_id', $user->id)
                                                   ->where('is_read', false);
                                    });
                        });
                    })
                    ->orderBy('sent_at', 'desc')
                    ->limit(5)
                    ->get();
            }

            // Lấy hoạt động sắp tới (5 sự kiện gần nhất)
            if (!empty($userClubs)) {
                $upcomingEvents = DB::table('events')
                    ->join('clubs', 'events.club_id', '=', 'clubs.id')
                    ->whereIn('events.club_id', $userClubs)
                    ->where('events.status', 'upcoming')
                    ->where('events.start_at', '>', now())
                    ->select(
                        'events.*',
                        'clubs.name as club_name',
                        'clubs.code as club_code'
                    )
                    ->orderBy('events.start_at', 'asc')
                    ->limit(5)
                    ->get();
            }

            // Lấy hoạt động gần đây (5 sự kiện đã tham gia)
            $recentActivities = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->join('clubs', 'events.club_id', '=', 'clubs.id')
                ->where('event_registrations.user_id', $user->id)
                ->where('event_registrations.status', 'attended')
                ->select(
                    'events.*',
                    'clubs.name as club_name',
                    'clubs.code as club_code',
                    'event_registrations.activity_points',
                    'event_registrations.created_at as attended_at'
                )
                ->orderBy('events.start_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('student.home', compact('user', 'memberships', 'stats', 'recentNotifications', 'upcomingEvents', 'recentActivities', 'userClubs'));
    }

    /**
     * Trang "CLB của tôi" - hiển thị danh sách CLB với dashboard mini
     */
    public function myClubs()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy danh sách CLB từ club_members (đã phê duyệt)
        $clubsFromMembers = DB::table('club_members')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.user_id', $user->id)
            ->where('club_members.status', 'approved')
            ->where('clubs.status', 'active')
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                'clubs.logo',
                'clubs.field',
                'clubs.status as club_status',
                'club_members.position',
                'club_members.joined_date',
                'club_members.created_at'
            )
            ->get();

        // Lấy danh sách CLB mà user là chủ nhiệm (owner_id) - CHỈ khi CLB đó CHƯA có chủ nhiệm trong club_members
        $clubsAsOwner = DB::table('clubs')
            ->where('owner_id', $user->id)
            ->where('status', 'active')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('club_members')
                    ->whereColumn('club_members.club_id', 'clubs.id')
                    ->where('club_members.position', 'chairman')
                    ->where('club_members.status', 'approved');
            })
            ->select(
                'clubs.id',
                'clubs.name',
                'clubs.code',
                'clubs.logo',
                'clubs.field',
                'clubs.status as club_status',
                DB::raw("'chairman' as position"),
                DB::raw("NULL as joined_date"),
                'clubs.created_at'
            )
            ->get();

        // Merge và loại bỏ trùng lặp (ưu tiên dữ liệu từ club_members nếu có)
        $myClubsAll = collect();
        $clubIds = [];

        // Thêm CLB từ club_members trước
        foreach ($clubsFromMembers as $club) {
            $myClubsAll->push($club);
            $clubIds[] = $club->id;
        }

        // Thêm CLB từ owner_id nếu chưa có trong danh sách
        foreach ($clubsAsOwner as $club) {
            if (!in_array($club->id, $clubIds)) {
                $myClubsAll->push($club);
                $clubIds[] = $club->id;
            }
        }

        // Sắp xếp: ưu tiên CLB có chức vụ (chairman, vice_chairman, etc.) lên trên, thành viên thường ở dưới
        $myClubsAll = $myClubsAll->sortBy(function($club) {
            $priority = [
                'chairman' => 1,
                'vice_chairman' => 2,
                'secretary' => 3,
                'head_expertise' => 4,
                'head_media' => 5,
                'head_events' => 6,
                'treasurer' => 7,
            ];
            return $priority[$club->position ?? 'member'] ?? 8;
        })->values();

        // Lấy đơn đăng ký đang chờ duyệt
        $pendingRegistrations = DB::table('club_registrations')
            ->join('clubs', 'club_registrations.club_id', '=', 'clubs.id')
            ->where('club_registrations.user_id', $user->id)
            ->where('club_registrations.status', 'pending')
            ->select(
                'club_registrations.*',
                'clubs.name as club_name',
                'clubs.code as club_code'
            )
            ->get();

        // Tính toán thống kê cho từng CLB
        foreach ($myClubsAll as $club) {
            // Số lượng thành viên
            $club->member_count = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('status', 'approved')
                ->count();

            // Số sự kiện đã tham gia
            $club->events_attended = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->where('events.club_id', $club->id)
                ->where('event_registrations.user_id', $user->id)
                ->where('event_registrations.status', 'attended')
                ->count();

            // Sự kiện sắp tới
            $club->upcoming_events = DB::table('events')
                ->where('club_id', $club->id)
                ->where('status', 'upcoming')
                ->where('start_at', '>', now())
                ->count();

            // Tổng điểm hoạt động trong CLB này
            $club->activity_points = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->where('events.club_id', $club->id)
                ->where('event_registrations.user_id', $user->id)
                ->where('event_registrations.status', 'attended')
                ->sum('event_registrations.activity_points') ?? 0;

            // Kiểm tra có phải chủ nhiệm không
            // Ưu tiên kiểm tra từ club_members với position='chairman' trước
            $isChairmanFromMembers = ($club->position === 'chairman');
            
            // CHỈ khi KHÔNG có chairman trong club_members, mới kiểm tra owner_id
            if (!$isChairmanFromMembers) {
                $hasChairmanInMembers = DB::table('club_members')
                    ->where('club_id', $club->id)
                    ->where('position', 'chairman')
                    ->where('status', 'approved')
                    ->exists();
                
                if (!$hasChairmanInMembers) {
                    $isOwner = DB::table('clubs')
                        ->where('id', $club->id)
                        ->where('owner_id', $user->id)
                        ->exists();
                    $club->is_chairman = $isOwner;
                } else {
                    $club->is_chairman = false;
                }
            } else {
                $club->is_chairman = true;
            }
        }

        // Phân trang: 8 CLB mỗi trang (2 hàng x 4 CLB)
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 8;
        $currentItems = $myClubsAll->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $myClubs = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $myClubsAll->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
        );

        // Lấy đơn đề nghị CLB của user (chỉ hiển thị đơn đang chờ duyệt và bị từ chối, ẩn đơn đã duyệt vì CLB đã được tạo)
        $clubProposals = ClubProposal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.my-clubs', compact('myClubs', 'pendingRegistrations', 'clubProposals', 'user'));
    }

    /**
     * Xem chi tiết CLB
     */
    public function clubDetail($clubId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy thông tin CLB trước
        $club = Club::findOrFail($clubId);

        // Kiểm tra user có phải thành viên của CLB này không (từ club_members)
        $membership = DB::table('club_members')
            ->where('club_id', $clubId)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        // Kiểm tra user có phải chủ nhiệm của CLB này không
        // Ưu tiên kiểm tra từ club_members với position='chairman' trước
        $isChairmanFromMembers = DB::table('club_members')
            ->where('club_id', $clubId)
            ->where('user_id', $user->id)
            ->where('position', 'chairman')
            ->where('status', 'approved')
            ->exists();
        
        // CHỈ khi KHÔNG có chairman trong club_members, mới kiểm tra owner_id
        $isOwner = false;
        if (!$isChairmanFromMembers) {
            $hasChairmanInMembers = DB::table('club_members')
                ->where('club_id', $clubId)
                ->where('position', 'chairman')
                ->where('status', 'approved')
                ->exists();
            
            if (!$hasChairmanInMembers) {
                $isOwner = ($club->owner_id == $user->id && $club->status == 'active');
            }
        }

        // Nếu không phải thành viên và cũng không phải chủ nhiệm thì không cho xem
        if (!$membership && !$isOwner && !$isChairmanFromMembers) {
            return redirect()->route('student.my-clubs')
                ->with('error', 'Bạn không phải thành viên của CLB này.');
        }

        // Nếu là chủ nhiệm (từ club_members hoặc owner_id) nhưng chưa có trong club_members, tạo membership ảo
        if (($isOwner || $isChairmanFromMembers) && !$membership) {
            $membership = (object)[
                'position' => 'chairman',
                'status' => 'approved',
                'joined_date' => $club->created_at ? \Carbon\Carbon::parse($club->created_at)->toDateString() : now()->toDateString(),
            ];
        }

        // Lấy chủ nhiệm và ban điều hành
        // Ưu tiên lấy từ club_members với position='chairman' trước
        $chairman = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $clubId)
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

        $executives = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $clubId)
            ->whereIn('club_members.position', ['vice_chairman', 'secretary', 'head_expertise', 'head_media', 'head_events', 'treasurer'])
            ->where('club_members.status', 'approved')
            ->select(
                'users.id',
                'users.name',
                'users.student_code',
                'club_members.position'
            )
            ->get();

        // Danh sách thành viên
        $members = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $clubId)
            ->where('club_members.status', 'approved')
            ->select(
                'users.id',
                'users.name',
                'users.student_code',
                'users.email',
                'club_members.position',
                'club_members.joined_date'
            )
            ->orderByRaw("CASE 
                WHEN club_members.position = 'chairman' THEN 1
                WHEN club_members.position = 'vice_chairman' THEN 2
                WHEN club_members.position = 'secretary' THEN 3
                WHEN club_members.position = 'head_expertise' THEN 4
                WHEN club_members.position = 'head_media' THEN 5
                WHEN club_members.position = 'head_events' THEN 6
                WHEN club_members.position = 'treasurer' THEN 7
                ELSE 8
            END")
            ->orderBy('club_members.joined_date', 'asc')
            ->get();

        // Lấy search query cho sự kiện đang diễn ra
        $ongoingSearch = request('ongoing_search');

        // Lấy search và filter cho sự kiện (hỗ trợ cả tên cũ và mới)
        $eventSearch = request('event_search') ?: request('upcoming_search');
        $eventStatus = request('event_status') ?: request('upcoming_status'); // Trạng thái đăng ký: all, pending, approved, rejected, none
        $eventActivityType = request('event_activity_type') ?: request('upcoming_activity_type'); // Loại hoạt động
        $eventTab = request('event_tab', 'ongoing'); // Tab hiện tại: ongoing, upcoming, finished
        $ongoingSearch = request('ongoing_search'); // Giữ lại để tương thích

        // Sự kiện đang diễn ra
        $ongoingEventsQuery = DB::table('events')
            ->where('club_id', $clubId)
            ->where('status', 'ongoing');
        
        if ($ongoingSearch || ($eventTab == 'ongoing' && $eventSearch)) {
            $searchTerm = $ongoingSearch ?: $eventSearch;
            $ongoingEventsQuery->where('title', 'like', '%' . $searchTerm . '%');
        }
        if ($eventTab == 'ongoing' && $eventActivityType) {
            $ongoingEventsQuery->where('activity_type', $eventActivityType);
        }
        
        $ongoingEvents = $ongoingEventsQuery->orderBy('start_at', 'asc')->get();

        // Lấy search và filter cho sự kiện (hỗ trợ cả tên cũ và mới)
        $eventSearch = request('event_search') ?: request('upcoming_search');
        $eventStatus = request('event_status') ?: request('upcoming_status'); // Trạng thái đăng ký: all, pending, approved, rejected, none
        $eventActivityType = request('event_activity_type') ?: request('upcoming_activity_type'); // Loại hoạt động
        $eventTab = request('event_tab', 'ongoing'); // Tab hiện tại: ongoing, upcoming, finished

        // Sự kiện đang diễn ra
        $ongoingEventsQuery = DB::table('events')
            ->where('club_id', $clubId)
            ->where('status', 'ongoing');
        
        if ($eventTab == 'ongoing' && $eventSearch) {
            $ongoingEventsQuery->where('title', 'like', '%' . $eventSearch . '%');
        }
        if ($eventTab == 'ongoing' && $eventActivityType) {
            $ongoingEventsQuery->where('activity_type', $eventActivityType);
        }
        
        $ongoingEvents = $ongoingEventsQuery->orderBy('start_at', 'asc')->get();

        // Sự kiện sắp tới (bao gồm cả bị từ chối để hiển thị trạng thái)
        $upcomingEventsQuery = DB::table('events')
            ->where('club_id', $clubId)
            ->where('status', 'upcoming');
        
        // Filter theo tìm kiếm
        if ($eventTab == 'upcoming' && $eventSearch) {
            $upcomingEventsQuery->where('title', 'like', '%' . $eventSearch . '%');
        }
        
        // Filter theo loại hoạt động
        if ($eventTab == 'upcoming' && $eventActivityType) {
            $upcomingEventsQuery->where('activity_type', $eventActivityType);
        }
        
        $upcomingEvents = $upcomingEventsQuery->orderBy('start_at', 'asc')->get();


        // Sự kiện đã tham gia
        $attendedEvents = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.club_id', $clubId)
            ->where('event_registrations.user_id', $user->id)
            ->where('event_registrations.status', 'attended')
            ->select(
                'events.*',
                'event_registrations.activity_points',
                'event_registrations.created_at as registered_at'
            )
            ->orderBy('events.start_at', 'desc')
            ->get();

        // Tổng điểm hoạt động
        $totalActivityPoints = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.club_id', $clubId)
            ->where('event_registrations.user_id', $user->id)
            ->where('event_registrations.status', 'attended')
            ->sum('event_registrations.activity_points') ?? 0;

        // Điểm theo sự kiện
        $pointsByEvent = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('events.club_id', $clubId)
            ->where('event_registrations.user_id', $user->id)
            ->where('event_registrations.status', 'attended')
            ->select(
                'events.id',
                'events.title',
                'events.start_at',
                'event_registrations.activity_points'
            )
            ->orderBy('events.start_at', 'desc')
            ->get();

        // Xếp loại hoạt động
        $activityLevel = 'Chưa tích cực';
        if ($totalActivityPoints >= 200) {
            $activityLevel = 'Tích cực';
        } elseif ($totalActivityPoints >= 100) {
            $activityLevel = 'Bình thường';
        }

        // Kiểm tra có phải chủ nhiệm không
        // Ưu tiên kiểm tra từ club_members với position='chairman' trước
        $isChairman = ($membership->position === 'chairman' || $isChairmanFromMembers || $isOwner);

        // Lấy thông tin đăng ký sự kiện của user cho tất cả sự kiện sắp tới
        $eventRegistrations = [];
        if ($upcomingEvents->count() > 0) {
            // Lấy registration mới nhất cho mỗi event (nếu có nhiều registration)
            $registrations = DB::table('event_registrations')
                ->where('user_id', $user->id)
                ->whereIn('event_id', $upcomingEvents->pluck('id')->toArray())
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('event_id')
                ->map(function($group) {
                    return $group->first(); // Lấy registration mới nhất
                });
            
            foreach ($upcomingEvents as $event) {
                $event->registration = $registrations->get($event->id);
            }

            // Filter theo trạng thái đăng ký
            $statusFilter = $eventStatus ?: request('upcoming_status');
            if ($statusFilter && $eventTab == 'upcoming') {
                $upcomingEvents = $upcomingEvents->filter(function($event) use ($statusFilter) {
                    if ($statusFilter === 'none') {
                        return !isset($event->registration);
                    } else {
                        return isset($event->registration) && trim($event->registration->status) === trim($statusFilter);
                    }
                })->values();
            }

            // Sắp xếp sự kiện sắp tới theo logic:
            // 1. Chưa đăng ký (none) - ưu tiên cao nhất, hiển thị đầu tiên
            // 2. Chờ duyệt (pending) - hiển thị thứ 2
            // 3. Đã đăng ký (approved) - hiển thị thứ 3
            // 4. Bị từ chối (rejected) - hiển thị cuối cùng
            $upcomingEventsArray = $upcomingEvents->all();
            usort($upcomingEventsArray, function($a, $b) {
                $aReg = $a->registration ?? null;
                $bReg = $b->registration ?? null;
                
                // Xác định thứ tự ưu tiên: none (chưa đăng ký) = 1, pending (chờ duyệt) = 2, approved (đã đăng ký) = 3, rejected (bị từ chối) = 4
                $getPriority = function($reg) {
                    if (!$reg) return 1; // Chưa đăng ký - ưu tiên cao nhất
                    $status = trim($reg->status ?? '');
                    $statusOrder = ['pending' => 2, 'approved' => 3, 'rejected' => 4];
                    return $statusOrder[$status] ?? 999;
                };
                
                $aPriority = $getPriority($aReg);
                $bPriority = $getPriority($bReg);
                
                // Sắp xếp theo priority (số nhỏ hơn = ưu tiên cao hơn)
                if ($aPriority !== $bPriority) {
                    return $aPriority <=> $bPriority;
                }
                
                // Nếu cùng priority, sắp xếp theo thời gian
                if (!$aReg && !$bReg) {
                    return strtotime($a->created_at ?? $a->start_at) <=> strtotime($b->created_at ?? $b->start_at);
                } else if ($aReg && $bReg) {
                    return strtotime($aReg->created_at) <=> strtotime($bReg->created_at);
                }
                
                return 0;
            });
            $upcomingEvents = collect($upcomingEventsArray);
        }

        // Lấy danh sách đề xuất hoạt động của sinh viên cho CLB này
        $proposals = DB::table('events')
            ->where('club_id', $clubId)
            ->where('created_by', $user->id)
            ->whereNotNull('created_by')
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
                'events.updated_at'
            )
            ->orderBy('events.created_at', 'desc')
            ->get();

        // Lấy thông tin người duyệt từ notifications (nếu có)
        foreach ($proposals as $proposal) {
            // Khởi tạo các thuộc tính để tránh lỗi undefined property
            $proposal->approver_name = null;
            $proposal->approver_student_code = null;
            
            if (in_array($proposal->approval_status, ['approved', 'rejected'])) {
                $notification = DB::table('notifications')
                    ->where('body', 'like', '%' . $proposal->title . '%')
                    ->whereIn('title', [
                        'Đề xuất hoạt động đã được duyệt',
                        'Đề xuất hoạt động đã bị từ chối',
                        'Đề xuất hoạt động bị từ chối'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($notification) {
                    $approver = DB::table('users')
                        ->where('id', $notification->sender_id)
                        ->select('name', 'student_code')
                        ->first();
                    
                    if ($approver) {
                        $proposal->approver_name = $approver->name;
                        $proposal->approver_student_code = $approver->student_code;
                    } else {
                        // Nếu không tìm thấy từ notification, lấy chủ nhiệm/phó chủ nhiệm của CLB
                        $chairmanOrVice = DB::table('club_members')
                            ->join('users', 'club_members.user_id', '=', 'users.id')
                            ->where('club_members.club_id', $clubId)
                            ->whereIn('club_members.position', ['chairman', 'vice_chairman'])
                            ->where('club_members.status', 'approved')
                            ->select('users.name', 'users.student_code')
                            ->first();
                        
                        if ($chairmanOrVice) {
                            $proposal->approver_name = $chairmanOrVice->name;
                            $proposal->approver_student_code = $chairmanOrVice->student_code;
                        }
                    }
                } else {
                    // Nếu không tìm thấy notification, lấy chủ nhiệm/phó chủ nhiệm của CLB
                    $chairmanOrVice = DB::table('club_members')
                        ->join('users', 'club_members.user_id', '=', 'users.id')
                        ->where('club_members.club_id', $clubId)
                        ->whereIn('club_members.position', ['chairman', 'vice_chairman'])
                        ->where('club_members.status', 'approved')
                        ->select('users.name', 'users.student_code')
                        ->first();
                    
                    if ($chairmanOrVice) {
                        $proposal->approver_name = $chairmanOrVice->name;
                        $proposal->approver_student_code = $chairmanOrVice->student_code;
                    }
                }
            }
        }

        return view('student.club-detail', compact(
            'club',
            'membership',
            'chairman',
            'executives',
            'members',
            'ongoingEvents',
            'upcomingEvents',
            'attendedEvents',
            'totalActivityPoints',
            'pointsByEvent',
            'activityLevel',
            'isChairman',
            'user',
            'proposals'
        ));
    }

    /**
     * Đăng ký tham gia sự kiện
     */
    public function registerEvent(Request $request, $eventId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $event = Event::findOrFail($eventId);

        // Kiểm tra sự kiện có bị từ chối không
        if ($event->approval_status === 'rejected') {
            return back()->with('error', 'Sự kiện này đã bị từ chối và không thể đăng ký tham gia.');
        }

        // Kiểm tra user có phải thành viên của CLB không
        $membership = DB::table('club_members')
            ->where('club_id', $event->club_id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        if (!$membership) {
            return back()->with('error', 'Bạn không phải thành viên của CLB này.');
        }

        // Kiểm tra đã đăng ký chưa (chỉ cho phép đăng ký lại nếu đã bị từ chối hoặc đã hủy)
        $existing = DB::table('event_registrations')
            ->where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            // Nếu đã đăng ký và đang pending hoặc approved, không cho đăng ký lại
            if (in_array($existing->status, ['pending', 'approved', 'attended'])) {
                return back()->with('error', 'Bạn đã đăng ký tham gia sự kiện này rồi.');
            }
            // Nếu đã bị từ chối, cập nhật lại thành pending
            if ($existing->status === 'rejected') {
                DB::table('event_registrations')
                    ->where('id', $existing->id)
                    ->update([
                        'status' => 'pending',
                        'notes' => null, // Xóa lý do từ chối cũ
                        'updated_at' => now(),
                    ]);
                // Redirect đến tab events với hash để tự động scroll đến sự kiện
                return redirect()->route('student.club-detail', ['id' => $event->club_id])->with('success', 'Đăng ký tham gia sự kiện thành công! Đang chờ phê duyệt.')->with('scroll_to_event', $eventId);
            }
        }

        // Tạo đơn đăng ký mới (sử dụng updateOrInsert để tránh lỗi unique constraint)
        DB::table('event_registrations')->updateOrInsert(
            [
                'event_id' => $eventId,
                'user_id' => $user->id,
            ],
            [
                'status' => 'pending',
                'activity_points' => 0,
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Redirect đến tab events với hash để tự động scroll đến sự kiện
        return redirect()->route('student.club-detail', ['id' => $event->club_id])->with('success', 'Đăng ký tham gia sự kiện thành công! Đang chờ phê duyệt.')->with('scroll_to_event', $eventId);
    }

    /**
     * Hủy đăng ký sự kiện
     */
    public function cancelEventRegistration($registrationId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $registration = DB::table('event_registrations')
            ->where('id', $registrationId)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$registration) {
            return back()->with('error', 'Không thể hủy đăng ký.');
        }

        // Lấy thông tin sự kiện để redirect
        $event = DB::table('events')
            ->where('id', $registration->event_id)
            ->first();

        DB::table('event_registrations')
            ->where('id', $registrationId)
            ->delete();

        // Redirect đến tab events với hash để tự động scroll đến sự kiện
        return redirect()->route('student.club-detail', ['id' => $event->club_id])->with('success', 'Đã hủy đăng ký sự kiện.')->with('scroll_to_event', $event->id);
    }

    /**
     * Rời khỏi CLB
     */
    public function leaveClub($clubId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $membership = DB::table('club_members')
            ->where('club_id', $clubId)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        if (!$membership) {
            return back()->with('error', 'Bạn không phải thành viên của CLB này.');
        }

        // Chủ nhiệm không thể rời CLB
        if ($membership->position === 'chairman') {
            return back()->with('error', 'Chủ nhiệm không thể rời CLB. Vui lòng liên hệ quản trị viên.');
        }

        // Cập nhật trạng thái thành "left"
        DB::table('club_members')
            ->where('id', $membership->id)
            ->update([
                'status' => 'left',
                'updated_at' => now(),
            ]);

        // Xóa hoặc cập nhật đơn đăng ký hiện tại (nếu có status = 'approved' hoặc 'pending')
        // Không cập nhật thành 'left' vì enum không có giá trị này
        // Thay vào đó, xóa đơn đăng ký đã duyệt để cho phép đăng ký lại
        DB::table('club_registrations')
            ->where('club_id', $clubId)
            ->where('user_id', $user->id)
            ->whereIn('status', ['approved', 'pending'])
            ->delete();

        return redirect()->route('student.my-clubs')
            ->with('success', 'Bạn đã rời khỏi CLB thành công.');
    }

    /**
     * Trang Hồ Sơ Cá Nhân
     */
    public function profile()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Thống kê CLB
        $clubsJoined = DB::table('club_members')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        // Số sự kiện đã tham gia
        $eventsAttended = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('event_registrations.status', 'attended')
            ->count();

        // Tổng điểm hoạt động
        $totalActivityPoints = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('event_registrations.status', 'attended')
            ->sum('event_registrations.activity_points') ?? 0;

        // Xếp loại hoạt động
        $activityLevel = 'Chưa tích cực';
        if ($totalActivityPoints >= 200) {
            $activityLevel = 'Tích cực';
        } elseif ($totalActivityPoints >= 100) {
            $activityLevel = 'Bình thường';
        }

        // Lịch sử hoạt động
        $activityHistory = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('event_registrations.status', 'attended')
            ->select(
                'events.id',
                'events.title',
                'events.start_at',
                'events.end_at',
                'clubs.name as club_name',
                'event_registrations.activity_points',
                'event_registrations.updated_at as completed_at'
            )
            ->orderBy('events.start_at', 'desc')
            ->get();

        // Lấy danh sách CLB mà sinh viên đã tham gia để đề xuất hoạt động
        $userClubs = DB::table('club_members')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.user_id', $user->id)
            ->where('club_members.status', 'approved')
            ->select('clubs.id', 'clubs.name', 'clubs.code')
            ->get();

        return view('student.profile', compact(
            'user',
            'clubsJoined',
            'eventsAttended',
            'totalActivityPoints',
            'activityLevel',
            'activityHistory',
            'userClubs'
        ));
    }

    /**
     * Đề xuất hoạt động mới (Sinh viên)
     */
    public function proposeEvent(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy danh sách CLB mà sinh viên đã tham gia
        $userClubs = DB::table('club_members')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.user_id', $user->id)
            ->where('club_members.status', 'approved')
            ->select('clubs.id', 'clubs.name', 'clubs.code')
            ->get();

        // Lấy CLB được chọn từ query parameter (nếu có)
        $selectedClubId = $request->query('club_id');
        $selectedClub = null;
        if ($selectedClubId) {
            $selectedClub = DB::table('clubs')
                ->where('id', $selectedClubId)
                ->first();
            
            // Kiểm tra user có phải thành viên của CLB này không
            $membership = DB::table('club_members')
                ->where('club_id', $selectedClubId)
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->first();
            
            if (!$membership) {
                return redirect()->route('student.propose-event')
                    ->with('error', 'Bạn không phải thành viên của CLB này.');
            }
        }

        // Lấy thông tin chức vụ của user trong CLB được chọn
        $userPosition = null;
        if ($selectedClubId) {
            $membership = DB::table('club_members')
                ->where('club_id', $selectedClubId)
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->first();
            $userPosition = $membership ? $membership->position : null;
        }

        return view('student.propose-event', compact('userClubs', 'selectedClub', 'user', 'userPosition'));
    }

    /**
     * Lưu đề xuất hoạt động (Sinh viên)
     */
    public function storeProposedEvent(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
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

        // Kiểm tra sinh viên có phải thành viên của CLB không
        $membership = DB::table('club_members')
            ->where('club_id', $request->club_id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        if (!$membership) {
            return back()->with('error', 'Bạn không phải thành viên của CLB này.');
        }

        // Xử lý file đính kèm
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('event_attachments', $fileName, 'public');
        }

        Event::create([
            'club_id' => $request->club_id,
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
            'approval_status' => 'pending', // Chờ admin duyệt
            'created_by' => $user->id, // Lưu người tạo
        ]);

        // Redirect về trang chi tiết CLB nếu có club_id trong request
        if ($request->filled('club_id')) {
            return redirect()->route('student.club-detail', $request->club_id)
                ->with('success', 'Đề xuất hoạt động thành công! Đang chờ phê duyệt từ quản trị viên.');
        }
        
        return redirect()->route('student.profile')
            ->with('success', 'Đề xuất hoạt động thành công! Đang chờ phê duyệt từ quản trị viên.');
    }

    /**
     * Cập nhật hồ sơ cá nhân
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'department' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        // Cập nhật thông tin cơ bản
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('gender')) {
            $user->gender = $request->gender;
        }
        if ($request->has('date_of_birth')) {
            $user->date_of_birth = $request->date_of_birth;
        }
        if ($request->has('department')) {
            $user->department = $request->department;
        }
        if ($request->has('class')) {
            $user->class = $request->class;
        }
        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }

        // Xử lý ảnh đại diện
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // Đổi mật khẩu
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Cập nhật hồ sơ thành công!');
    }

    /**
     * Trang Danh Sách CLB (Tất cả CLB)
     */
    public function allClubs(Request $request)
    {
        $keyword = $request->keyword ?? '';
        $field = $request->field ?? '';
        $minMembers = $request->min_members ?? '';

        $query = DB::table('clubs')
            ->where('status', 'active')
            ->select('clubs.*');

        // Tìm kiếm theo tên
        if ($keyword) {
            $query->where('clubs.name', 'like', "%{$keyword}%");
        }

        // Lọc theo lĩnh vực (có thể là club_type hoặc field)
        if ($field) {
            $query->where(function($q) use ($field) {
                $q->where('clubs.field', $field)
                  ->orWhere('clubs.club_type', $field);
            });
        }

        // Lấy danh sách CLB
        $clubs = $query->orderBy('clubs.created_at', 'desc')->get();

        $user = Auth::user();
        $userClubIds = [];
        $userRegistrationIds = [];
        
        if ($user) {
            // CLB đã tham gia
            $userClubIds = DB::table('club_members')
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->pluck('club_id')
                ->toArray();

            // CLB đã đăng ký nhưng chờ duyệt
            $userRegistrationIds = DB::table('club_registrations')
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->pluck('club_id')
                ->toArray();
        }

        // Tính toán thống kê cho từng CLB và sắp xếp
        $clubsWithStats = [];
        foreach ($clubs as $club) {
            // Số thành viên
            $club->member_count = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('status', 'approved')
                ->count();

            // Số sự kiện
            $club->event_count = DB::table('events')
                ->where('club_id', $club->id)
                ->count();

            // Lọc theo số thành viên
            if ($minMembers && $club->member_count < (int)$minMembers) {
                continue;
            }

            // Đánh dấu trạng thái CLB để sắp xếp
            $club->is_member = in_array($club->id, $userClubIds); // Đã tham gia
            $club->is_pending = in_array($club->id, $userRegistrationIds); // Đang chờ phê duyệt
            $clubsWithStats[] = $club;
        }

        // Sắp xếp theo thứ tự: Đã tham gia > Đang chờ phê duyệt > Chưa tham gia
        usort($clubsWithStats, function($a, $b) {
            // Ưu tiên 1: Đã tham gia
            if ($a->is_member && !$b->is_member) {
                return -1; // a lên trước
            }
            if (!$a->is_member && $b->is_member) {
                return 1; // b lên trước
            }
            
            // Nếu cả hai đều đã tham gia hoặc chưa tham gia, kiểm tra trạng thái chờ duyệt
            if ($a->is_member && $b->is_member) {
                // Cả hai đều đã tham gia, giữ nguyên thứ tự
                return 0;
            }
            
            // Ưu tiên 2: Đang chờ phê duyệt
            if ($a->is_pending && !$b->is_pending) {
                return -1; // a (đang chờ) lên trước b (chưa tham gia)
            }
            if (!$a->is_pending && $b->is_pending) {
                return 1; // b (đang chờ) lên trước a (chưa tham gia)
            }
            
            // Cả hai cùng trạng thái, giữ nguyên thứ tự
            return 0;
        });

        $clubsCollection = collect($clubsWithStats);
        
        // Phân trang: 8 CLB mỗi trang (2 hàng x 4 CLB)
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 8;
        $currentItems = $clubsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $clubs = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $clubsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Lấy danh sách lĩnh vực để filter (ưu tiên club_type, nếu không có thì dùng field)
        $fields = DB::table('clubs')
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNotNull('club_type')->orWhereNotNull('field');
            })
            ->select('club_type', 'field')
            ->get()
            ->map(function($club) {
                return $club->club_type ?? $club->field;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return view('student.all-clubs', compact(
            'clubs',
            'fields',
            'keyword',
            'field',
            'minMembers',
            'userClubIds',
            'userRegistrationIds',
            'user'
        ));
    }

    /**
     * Xem chi tiết CLB công khai (cho người chưa tham gia)
     */
    public function clubPublicDetail($clubId)
    {
        $club = Club::findOrFail($clubId);

        if ($club->status !== 'active') {
            return back()->with('error', 'CLB này không hoạt động.');
        }

        // Chủ nhiệm
        $chairman = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $clubId)
            ->where('club_members.position', 'chairman')
            ->where('club_members.status', 'approved')
            ->select('users.id', 'users.name', 'users.student_code', 'users.email', 'users.avatar')
            ->first();

        // Ban điều hành (bao gồm cả Phó chủ nhiệm)
        $executives = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $clubId)
            ->whereIn('club_members.position', ['vice_chairman', 'secretary', 'head_expertise', 'head_media', 'head_events', 'treasurer'])
            ->where('club_members.status', 'approved')
            ->select(
                'users.id',
                'users.name',
                'users.student_code',
                'club_members.position'
            )
            ->get();

        // Số thành viên
        $memberCount = DB::table('club_members')
            ->where('club_id', $clubId)
            ->where('status', 'approved')
            ->count();

        // Số sự kiện đã tổ chức
        $totalEvents = DB::table('events')
            ->where('club_id', $clubId)
            ->where('status', 'finished')
            ->count();

        // Sự kiện sắp diễn ra (upcoming và ongoing)
        $upcomingEvents = DB::table('events')
            ->where('club_id', $clubId)
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->orderBy('start_at', 'asc')
            ->limit(5)
            ->get();

        // Hoạt động đã tổ chức (sự kiện đã kết thúc)
        $pastEvents = DB::table('events')
            ->where('club_id', $clubId)
            ->where('status', 'finished')
            ->orderBy('start_at', 'desc')
            ->limit(10)
            ->get();

        // Tính mức độ hoạt động
        $activityLevel = 'Thấp';
        if ($totalEvents >= 10) {
            $activityLevel = 'Cao';
        } elseif ($totalEvents >= 5) {
            $activityLevel = 'Trung bình';
        }

        $user = Auth::user();
        $isMember = false;
        $hasRegistration = false;
        $registrationId = null;

        if ($user) {
            // Kiểm tra đã là thành viên chưa
            $isMember = DB::table('club_members')
                ->where('club_id', $clubId)
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists();

            // Kiểm tra đã đăng ký chưa
            $registration = DB::table('club_registrations')
                ->where('club_id', $clubId)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if ($registration) {
                $hasRegistration = true;
                $registrationId = $registration->id;
            }
        }

        return view('student.club-public-detail', compact(
            'club',
            'chairman',
            'executives',
            'memberCount',
            'totalEvents',
            'upcomingEvents',
            'pastEvents',
            'activityLevel',
            'isMember',
            'hasRegistration',
            'registrationId',
            'user'
        ));
    }

    /**
     * Đăng ký tham gia CLB
     */
    public function registerClub(Request $request, $clubId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $club = Club::findOrFail($clubId);

        if ($club->status !== 'active') {
            return back()->with('error', 'CLB này không hoạt động.');
        }

        // Kiểm tra đã là thành viên chưa (chỉ kiểm tra status = 'approved')
        $isMember = DB::table('club_members')
            ->where('club_id', $clubId)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();

        if ($isMember) {
            return back()->with('error', 'Bạn đã là thành viên của CLB này.');
        }

        // Kiểm tra đã từng tham gia và rời CLB chưa
        $hasLeft = DB::table('club_members')
            ->where('club_id', $clubId)
            ->where('user_id', $user->id)
            ->where('status', 'left')
            ->exists();

        // Kiểm tra đã đăng ký chưa
        $existing = DB::table('club_registrations')
            ->where('club_id', $clubId)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'pending') {
                return back()->with('error', 'Bạn đã gửi đơn đăng ký. Vui lòng chờ phê duyệt.');
            } elseif ($existing->status === 'approved') {
                // Nếu đã được duyệt nhưng chưa có trong club_members với status approved, cho phép đăng ký lại
                $isApprovedMember = DB::table('club_members')
                    ->where('club_id', $clubId)
                    ->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->exists();
                
                if (!$isApprovedMember) {
                    // Cập nhật đơn cũ thành pending và tạo đơn mới
                    DB::table('club_registrations')
                        ->where('id', $existing->id)
                        ->update([
                            'reason' => $request->reason ?? null,
                            'status' => 'pending',
                            'updated_at' => now(),
                        ]);
                    return back()->with('success', 'Đăng ký tham gia CLB thành công! Đang chờ phê duyệt.');
                } else {
                    return back()->with('error', 'Bạn đã được phê duyệt tham gia CLB này.');
                }
            } else {
                // Nếu đã bị từ chối hoặc có đơn cũ, cập nhật lại
                DB::table('club_registrations')
                    ->where('id', $existing->id)
                    ->update([
                        'reason' => $request->reason ?? null,
                        'status' => 'pending',
                        'updated_at' => now(),
                    ]);
                return back()->with('success', 'Đăng ký tham gia CLB thành công! Đang chờ phê duyệt.');
            }
        }

        // Tạo đơn đăng ký mới
        DB::table('club_registrations')->insert([
            'club_id' => $clubId,
            'user_id' => $user->id,
            'reason' => $request->reason ?? null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Đăng ký tham gia CLB thành công! Đang chờ phê duyệt.');
    }

    /**
     * Trang Hoạt Động CLB (Tất cả sự kiện)
     */
    public function activities(Request $request)
    {
        $clubId = $request->club_id ?? '';
        $timeFilter = $request->time_filter ?? '';
        $statusFilter = $request->status_filter ?? '';

        $query = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('clubs.status', 'active')
            ->select(
                'events.*',
                'clubs.name as club_name',
                'clubs.code as club_code',
                'clubs.logo as club_logo'
            );

        // Lọc theo CLB
        if ($clubId) {
            $query->where('events.club_id', $clubId);
        }

        // Lọc theo thời gian
        if ($timeFilter === 'week') {
            $query->where('events.start_at', '>=', now())
                  ->where('events.start_at', '<=', now()->addWeek());
        } elseif ($timeFilter === 'month') {
            $query->where('events.start_at', '>=', now())
                  ->where('events.start_at', '<=', now()->addMonth());
        }

        // Lọc theo trạng thái
        if ($statusFilter) {
            $query->where('events.status', $statusFilter);
        } else {
            // Mặc định chỉ hiển thị sự kiện sắp diễn ra và đang diễn ra
            $query->whereIn('events.status', ['upcoming', 'ongoing']);
        }

        $eventsAll = $query->orderBy('events.start_at', 'asc')->get();

        // Tính số người tham gia cho mỗi sự kiện
        foreach ($eventsAll as $event) {
            $event->participant_count = DB::table('event_registrations')
                ->where('event_id', $event->id)
                ->whereIn('status', ['approved', 'attended'])
                ->count();
        }

        // Phân trang: 8 sự kiện mỗi trang (2 hàng x 4 sự kiện)
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 8;
        $currentItems = $eventsAll->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $events = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $eventsAll->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Lấy danh sách CLB để filter
        $clubs = DB::table('clubs')
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        $user = Auth::user();
        $userEventRegistrations = [];
        
        if ($user) {
            $userEventRegistrations = DB::table('event_registrations')
                ->where('user_id', $user->id)
                ->pluck('status', 'event_id')
                ->toArray();
        }

        return view('student.activities', compact(
            'events',
            'clubs',
            'clubId',
            'timeFilter',
            'statusFilter',
            'userEventRegistrations',
            'user'
        ));
    }

    /**
     * Xem chi tiết sự kiện công khai
     */
    public function activityDetail($eventId)
    {
        $event = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('events.id', $eventId)
            ->where('clubs.status', 'active')
            ->select(
                'events.*',
                'clubs.name as club_name',
                'clubs.code as club_code',
                'clubs.logo as club_logo'
            )
            ->first();

        if (!$event) {
            return back()->with('error', 'Sự kiện không tồn tại.');
        }

        // Số người tham gia
        $participantCount = DB::table('event_registrations')
            ->where('event_id', $eventId)
            ->whereIn('status', ['approved', 'attended'])
            ->count();

        // Danh sách người tham gia (nếu sự kiện đã kết thúc hoặc đang diễn ra)
        $participants = collect();
        if (in_array($event->status, ['ongoing', 'finished'])) {
            $participants = DB::table('event_registrations')
                ->join('users', 'event_registrations.user_id', '=', 'users.id')
                ->where('event_registrations.event_id', $eventId)
                ->whereIn('event_registrations.status', ['approved', 'attended'])
                ->select(
                    'users.id',
                    'users.name',
                    'users.student_code',
                    'event_registrations.status',
                    'event_registrations.activity_points'
                )
                ->orderBy('event_registrations.created_at', 'asc')
                ->get();
        }

        $user = Auth::user();
        $userRegistration = null;
        $isMember = false;
        $hasClubRegistration = false;

        if ($user) {
            // Kiểm tra đã đăng ký chưa
            $userRegistration = DB::table('event_registrations')
                ->where('event_id', $eventId)
                ->where('user_id', $user->id)
                ->select('*')
                ->first();

            // Kiểm tra có phải thành viên của CLB không
            $isMember = DB::table('club_members')
                ->where('club_id', $event->club_id)
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists();

            // Kiểm tra đã đăng ký vào CLB chưa (chờ duyệt hoặc đã duyệt)
            $hasClubRegistration = DB::table('club_registrations')
                ->where('club_id', $event->club_id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();
        }

        return view('student.activity-detail', compact(
            'event',
            'participantCount',
            'participants',
            'userRegistration',
            'isMember',
            'hasClubRegistration',
            'user'
        ));
    }

    /**
     * Xem chi tiết đề xuất hoạt động (Sinh viên)
     */
    public function proposalDetail($proposalId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy thông tin đề xuất
        $proposal = DB::table('events')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('events.id', $proposalId)
            ->where('events.created_by', $user->id) // Chỉ xem đề xuất của chính mình
            ->whereNotNull('events.created_by')
            ->select(
                'events.*',
                'clubs.name as club_name',
                'clubs.code as club_code'
            )
            ->first();

        if (!$proposal) {
            return back()->with('error', 'Đề xuất không tồn tại hoặc bạn không có quyền xem.');
        }

        // Lấy thông tin người đề xuất
        $proposer = DB::table('users')
            ->where('id', $proposal->created_by)
            ->first();

        // Lấy thông tin người duyệt từ notifications (nếu có)
        $approver = null;
        if (in_array($proposal->approval_status, ['approved', 'rejected'])) {
            $notification = DB::table('notifications')
                ->where('body', 'like', '%' . $proposal->title . '%')
                ->whereIn('title', [
                    'Đề xuất hoạt động đã được duyệt',
                    'Đề xuất hoạt động đã bị từ chối',
                    'Đề xuất hoạt động bị từ chối'
                ])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($notification) {
                $approver = DB::table('users')
                    ->where('id', $notification->sender_id)
                    ->select('name', 'student_code', 'email')
                    ->first();
            }
            
            // Nếu không tìm thấy từ notification, lấy chủ nhiệm/phó chủ nhiệm của CLB
            if (!$approver) {
                $approver = DB::table('club_members')
                    ->join('users', 'club_members.user_id', '=', 'users.id')
                    ->where('club_members.club_id', $proposal->club_id)
                    ->whereIn('club_members.position', ['chairman', 'vice_chairman'])
                    ->where('club_members.status', 'approved')
                    ->select('users.name', 'users.student_code', 'users.email')
                    ->first();
            }
        }

        return view('student.proposal-detail', compact('proposal', 'proposer', 'user', 'approver'));
    }

    /**
     * Trang Cài đặt tài khoản
     */
    public function settings()
    {
        $user = Auth::user();
        return view('student.settings', compact('user'));
    }

    /**
     * Cập nhật cài đặt tài khoản
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $settingsType = $request->input('settings_type', 'general');
        
        if ($settingsType === 'security') {
            $user->two_factor_enabled = $request->has('two_factor_enabled') ? true : false;
        } elseif ($settingsType === 'notifications') {
            $user->email_notifications = $request->has('email_notifications') ? true : false;
            $user->event_notifications = $request->has('event_notifications') ? true : false;
            $user->club_notifications = $request->has('club_notifications') ? true : false;
        } elseif ($settingsType === 'general') {
            $request->validate([
                'language' => 'nullable|in:vi,en',
            ]);
            
            $user->language = $request->input('language', 'vi');
            $user->dark_mode = $request->has('dark_mode') ? true : false;
        }

        $user->save();

        return redirect()->route('student.settings')
            ->with('success', 'Cài đặt đã được cập nhật thành công!');
    }

    /**
     * Hiển thị form đổi mật khẩu
     */
    public function showChangePasswordForm()
    {
        $user = Auth::user();
        return view('student.change-password', compact('user'));
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('student.change-password')
            ->with('success', 'Mật khẩu đã được thay đổi thành công!');
    }

    /**
     * Trang Thông báo
     */
    public function notifications()
    {
        $user = Auth::user();
        
        // Lấy thông báo từ các sự kiện và CLB
        $notifications = collect();
        
        // Thông báo từ đơn đăng ký CLB
        $clubRegistrations = DB::table('club_registrations')
            ->join('clubs', 'club_registrations.club_id', '=', 'clubs.id')
            ->where('club_registrations.user_id', $user->id)
            ->select(
                'club_registrations.*',
                'clubs.name as club_name',
                'clubs.code as club_code'
            )
            ->orderBy('club_registrations.created_at', 'desc')
            ->get()
            ->map(function($item) {
                return (object) [
                    'id' => $item->id,
                    'type' => 'club_registration',
                    'title' => 'Đơn đăng ký CLB',
                    'message' => 'Đơn đăng ký CLB ' . $item->club_name . ' của bạn ' . 
                                ($item->status === 'approved' ? 'đã được duyệt' : 
                                 ($item->status === 'rejected' ? 'đã bị từ chối' : 'đang chờ duyệt')),
                    'status' => $item->status,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });
        
        $notifications = $notifications->merge($clubRegistrations);
        
        // Sắp xếp theo thời gian mới nhất
        $notifications = $notifications->sortByDesc('created_at')->values();

        return view('student.notifications', compact('notifications', 'user'));
    }

    /**
     * THỐNG KÊ - CÁ NHÂN (SINH VIÊN)
     * Trang tổng quan với tất cả tabs (giống profile)
     */
    public function personalStatistics(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Load tất cả data cho 4 tabs
        // Tab 1: Hoạt động đã tham gia
        $activitiesQuery = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('events.approval_status', 'approved');

        if ($request->club_id) {
            $activitiesQuery->where('events.club_id', $request->club_id);
        }
        if ($request->start_date) {
            $activitiesQuery->where('events.start_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $activitiesQuery->where('events.start_at', '<=', $request->end_date . ' 23:59:59');
        }
        if ($request->status && $request->status != 'all') {
            $activitiesQuery->where('event_registrations.status', $request->status);
        }
        if ($request->search) {
            $search = $request->search;
            $activitiesQuery->where(function($q) use ($search) {
                $q->where('events.title', 'like', "%{$search}%")
                  ->orWhere('clubs.name', 'like', "%{$search}%");
            });
        }

        $activities = $activitiesQuery->select(
                'event_registrations.id as registration_id',
                'event_registrations.status as registration_status',
                'event_registrations.activity_points',
                'event_registrations.created_at as registered_at',
                'events.id as event_id',
                'events.title',
                'events.description',
                'events.start_at',
                'events.end_at',
                'events.location',
                'events.status as event_status',
                'clubs.id as club_id',
                'clubs.name as club_name',
                'clubs.code as club_code'
            )
            ->orderBy('events.start_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalRegistered = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('events.approval_status', 'approved')
            ->count();

        $attended = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('events.approval_status', 'approved')
            ->where('event_registrations.status', 'attended')
            ->count();

        $absent = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('events.approval_status', 'approved')
            ->whereIn('event_registrations.status', ['approved'])
            ->where('events.status', 'finished')
            ->count();

        $cancelled = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('events.approval_status', 'approved')
            ->where('events.status', 'cancelled')
            ->count();

        // Tab 2: Điểm hoạt động
        $totalPoints = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('events.approval_status', 'approved')
            ->where('event_registrations.status', 'attended')
            ->sum('event_registrations.activity_points') ?? 0;

        $pointsByYear = [];
        $currentYear = date('Y');
        for ($year = $currentYear - 2; $year <= $currentYear; $year++) {
            $points = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->where('event_registrations.user_id', $user->id)
                ->where('events.approval_status', 'approved')
                ->where('event_registrations.status', 'attended')
                ->whereYear('events.start_at', $year)
                ->sum('event_registrations.activity_points') ?? 0;
            
            if ($points > 0) {
                $pointsByYear[$year] = $points;
            }
        }

        $pointsQuery = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('events.approval_status', 'approved')
            ->where('event_registrations.status', 'attended')
            ->where('event_registrations.activity_points', '>', 0);

        if ($request->year) {
            $pointsQuery->whereYear('events.start_at', $request->year);
        }
        if ($request->club_id) {
            $pointsQuery->where('events.club_id', $request->club_id);
        }

        $pointsDetail = $pointsQuery->select(
                'events.id',
                'events.title',
                'events.start_at',
                'events.end_at',
                'clubs.name as club_name',
                'clubs.code as club_code',
                'event_registrations.activity_points',
                'event_registrations.updated_at as point_date'
            )
            ->orderBy('events.start_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Tab 3: Lịch sử tham gia CLB
        $clubHistoryQuery = DB::table('club_members')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.user_id', $user->id);

        if ($request->status && $request->status != 'all') {
            if ($request->status == 'active') {
                $clubHistoryQuery->where('club_members.status', 'approved');
            } elseif ($request->status == 'left') {
                $clubHistoryQuery->where('club_members.status', 'left');
            }
        }
        if ($request->search) {
            $search = $request->search;
            $clubHistoryQuery->where(function($q) use ($search) {
                $q->where('clubs.name', 'like', "%{$search}%")
                  ->orWhere('clubs.code', 'like', "%{$search}%");
            });
        }

        $clubHistory = $clubHistoryQuery->select(
                'club_members.id',
                'club_members.position',
                'club_members.status',
                'club_members.created_at as joined_at',
                DB::raw("CASE WHEN club_members.status = 'left' THEN club_members.updated_at ELSE NULL END as left_at"),
                'clubs.id as club_id',
                'clubs.name as club_name',
                'clubs.code as club_code',
                'clubs.field as club_field',
                'clubs.description as club_description'
            )
            ->orderBy('club_members.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalClubs = DB::table('club_members')
            ->where('user_id', $user->id)
            ->count();

        $activeClubs = DB::table('club_members')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $leftClubs = $totalClubs - $activeClubs;

        // Tab 4: Lịch sử vi phạm
        $violationsQuery = DB::table('events')
            ->join('event_registrations', 'events.id', '=', 'event_registrations.event_id')
            ->join('clubs', 'events.club_id', '=', 'clubs.id')
            ->where('event_registrations.user_id', $user->id)
            ->where('events.approval_status', 'approved')
            ->whereNotNull('events.violation_type');

        if ($request->severity && $request->severity != 'all') {
            $violationsQuery->where('events.violation_severity', $request->severity);
        }
        if ($request->club_id) {
            $violationsQuery->where('events.club_id', $request->club_id);
        }
        if ($request->start_date) {
            $violationsQuery->where('events.violation_detected_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $violationsQuery->where('events.violation_detected_at', '<=', $request->end_date . ' 23:59:59');
        }

        $violations = $violationsQuery->select(
                'events.id',
                'events.title',
                'events.violation_type',
                'events.violation_severity',
                'events.violation_status',
                'events.violation_notes',
                'events.violation_detected_at',
                'events.violation_recorded_by',
                'clubs.name as club_name',
                'clubs.code as club_code'
            )
            ->orderBy('events.violation_detected_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalViolations = DB::table('events')
            ->join('event_registrations', 'events.id', '=', 'event_registrations.event_id')
            ->where('event_registrations.user_id', $user->id)
            ->where('events.approval_status', 'approved')
            ->whereNotNull('events.violation_type')
            ->count();

        $violationsBySeverity = DB::table('events')
            ->join('event_registrations', 'events.id', '=', 'event_registrations.event_id')
            ->where('event_registrations.user_id', $user->id)
            ->where('events.approval_status', 'approved')
            ->whereNotNull('events.violation_type')
            ->select('events.violation_severity', DB::raw('COUNT(*) as count'))
            ->groupBy('events.violation_severity')
            ->get()
            ->pluck('count', 'violation_severity');

        // Lấy danh sách CLB để filter
        $clubs = DB::table('club_members')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.user_id', $user->id)
            ->where('club_members.status', 'approved')
            ->select('clubs.id', 'clubs.name', 'clubs.code')
            ->get();

        return view('student.personal-statistics.index', compact(
            'user',
            'activities',
            'totalRegistered',
            'attended',
            'absent',
            'cancelled',
            'totalPoints',
            'pointsByYear',
            'pointsDetail',
            'clubHistory',
            'totalClubs',
            'activeClubs',
            'leftClubs',
            'violations',
            'totalViolations',
            'violationsBySeverity',
            'clubs'
        ));
    }

    /**
     * 1. Hoạt động đã tham gia (redirect về trang chính với tab)
     */
    public function personalStatisticsActivities(Request $request)
    {
        return redirect()->route('student.personal-statistics', array_merge($request->all(), ['tab' => 'activities']));
    }

    /**
     * 2. Điểm hoạt động cá nhân (redirect về trang chính với tab)
     */
    public function personalStatisticsPoints(Request $request)
    {
        return redirect()->route('student.personal-statistics', array_merge($request->all(), ['tab' => 'points']));
    }

    /**
     * 3. Lịch sử tham gia CLB (redirect về trang chính với tab)
     */
    public function personalStatisticsClubHistory(Request $request)
    {
        return redirect()->route('student.personal-statistics', array_merge($request->all(), ['tab' => 'club-history']));
    }

    /**
     * 4. Lịch sử vi phạm (nếu có) (redirect về trang chính với tab)
     */
    public function personalStatisticsViolations(Request $request)
    {
        return redirect()->route('student.personal-statistics', array_merge($request->all(), ['tab' => 'violations']));
    }

    /**
     * Upload avatar cho student
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
