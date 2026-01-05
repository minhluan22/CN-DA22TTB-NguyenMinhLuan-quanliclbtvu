<?php

namespace App\Http\Middleware;

use Illuminate\Session\Middleware\StartSession as BaseStartSession;
use Illuminate\Support\Facades\Auth;

class StartSessionByRole extends BaseStartSession
{
    /**
     * Get the name of the session cookie.
     * Override để tách session cookie theo role (admin/student)
     * Cho phép đăng nhập nhiều tài khoản trên các tab khác nhau
     *
     * @return string
     */
    protected function getCookieName()
    {
        $request = request();
        
        // QUAN TRỌNG: Kiểm tra session '_auth_role' TRƯỚC TIÊN (được set khi đăng nhập)
        // Điều này đảm bảo khi đăng nhập, session được tạo với cookie name đúng ngay lập tức
        if ($request->hasSession()) {
            $authRole = $request->session()->get('_auth_role');
            if ($authRole === 'admin') {
                return 'admin_session';
            } elseif ($authRole === 'student') {
                return 'student_session';
            }
        }
        
        // Kiểm tra cookie 'auth_role' (được set sau khi redirect)
        $authRole = $request->cookie('auth_role');
        if ($authRole === 'admin') {
            return 'admin_session';
        } elseif ($authRole === 'student') {
            return 'student_session';
        }
        
        // Nếu chưa có cookie auth_role, kiểm tra user đã đăng nhập
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role_id == 1) { // Admin
                return 'admin_session';
            } elseif ($user->role_id == 2) { // Student
                return 'student_session';
            }
        }
        
        // Route admin: Luôn dùng admin_session
        if ($request->is('admin/*') || $request->is('admin')) {
            return 'admin_session';
        }
        
        // Route student: Luôn dùng student_session
        if ($request->is('student/*') || $request->is('student')) {
            return 'student_session';
        }
        
        // Route login POST: Kiểm tra referer, input, hoặc cookie để biết user muốn đăng nhập với role nào
        if ($request->is('login') && $request->isMethod('post')) {
            // Ưu tiên 1: Kiểm tra input 'intended_role' (được set bởi JavaScript)
            $intendedRole = $request->input('intended_role');
            if ($intendedRole === 'admin') {
                return 'admin_session';
            } elseif ($intendedRole === 'student') {
                return 'student_session';
            }
            
            // Ưu tiên 2: Kiểm tra referer để biết user đến từ đâu
            $referer = $request->header('referer');
            if ($referer && strpos($referer, '/admin') !== false) {
                return 'admin_session';
            } elseif ($referer && strpos($referer, '/student') !== false) {
                return 'student_session';
            }
            
            // Ưu tiên 3: Kiểm tra cookie
            $intendedRole = $request->cookie('intended_role');
            if ($intendedRole === 'admin') {
                return 'admin_session';
            } elseif ($intendedRole === 'student') {
                return 'student_session';
            }
        }
        
        // Route login GET: Kiểm tra cookie để biết user muốn đăng nhập với role nào
        if ($request->is('login') && $request->isMethod('get')) {
            $intendedRole = $request->cookie('intended_role');
            if ($intendedRole === 'admin') {
                return 'admin_session';
            } elseif ($intendedRole === 'student') {
                return 'student_session';
            }
        }
        
        // Route khác (guest, register, etc.) - dùng session mặc định
        return config('session.cookie', 'laravel_session');
    }
}

