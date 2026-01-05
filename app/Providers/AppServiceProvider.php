<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationRecipient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share unread notifications count for student header
        View::composer('student.header', function ($view) {
            $user = Auth::user();
            $unreadCount = 0;

            if ($user) {
                // Lấy danh sách CLB mà user đang tham gia
                $userClubs = DB::table('club_members')
                    ->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->pluck('club_id')
                    ->toArray();

                // Đếm thông báo chưa đọc từ Admin và Chủ nhiệm CLB
                if (!empty($userClubs)) {
                    $unreadCount = NotificationRecipient::where('user_id', $user->id)
                        ->where('is_read', false)
                        ->whereHas('notification', function($query) use ($userClubs) {
                            $query->where('status', 'sent')
                                ->where(function($q) use ($userClubs) {
                                    // Thông báo từ Admin gửi đến CLB user tham gia hoặc toàn hệ thống
                                    $q->where(function($adminQ) use ($userClubs) {
                                        $adminQ->where('notification_source', 'admin')
                                             ->where(function($targetQ) use ($userClubs) {
                                                 // Gửi đến CLB cụ thể
                                                 foreach ($userClubs as $clubId) {
                                                     $targetQ->orWhereJsonContains('target_ids', $clubId);
                                                 }
                                                 // Hoặc gửi đến toàn bộ người dùng/sinh viên
                                                 $targetQ->orWhereIn('target_type', ['all', 'students']);
                                             });
                                    })
                                    // Hoặc thông báo nội bộ CLB (do chủ nhiệm gửi)
                                    ->orWhere(function($clubQ) use ($userClubs) {
                                        $clubQ->where('notification_source', 'club')
                                              ->whereIn('club_id', $userClubs);
                                    });
                                });
                        })
                        ->count();
                } else {
                    // Nếu user chưa tham gia CLB nào, chỉ đếm thông báo Admin gửi toàn hệ thống
                    $unreadCount = NotificationRecipient::where('user_id', $user->id)
                        ->where('is_read', false)
                        ->whereHas('notification', function($query) {
                            $query->where('status', 'sent')
                                ->where('notification_source', 'admin')
                                ->whereIn('target_type', ['all', 'students']);
                        })
                        ->count();
                }
            }

            $view->with('unreadNotifications', $unreadCount);
        });
    }
}
