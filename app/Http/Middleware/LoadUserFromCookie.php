<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoadUserFromCookie
{
    /**
     * Load user từ cookie nếu session không có user
     * Giúp duy trì đăng nhập khi chuyển giữa các route với cookie name khác nhau
     */
    public function handle($request, Closure $next)
    {
        // Chỉ xử lý nếu chưa có user đăng nhập
        if (!Auth::check()) {
            // Kiểm tra cookie 'auth_user_id' và 'auth_role'
            $userId = $request->cookie('auth_user_id');
            $userRole = $request->cookie('auth_role');
            
            if ($userId && $userRole) {
                // Load user từ database với relationship role
                $user = User::with('role')->find($userId);
                
                if ($user && $user->status == 1) {
                    // Kiểm tra role có khớp không
                    $isAdmin = $user->role && $user->role->name === 'Admin';
                    $expectedRole = $isAdmin ? 'admin' : 'student';
                    
                    if ($userRole === $expectedRole) {
                        // Đăng nhập user vào session hiện tại
                        // Sử dụng remember = false để không tạo remember token
                        Auth::login($user, false);
                    }
                }
            }
        }
        
        return $next($request);
    }
}

