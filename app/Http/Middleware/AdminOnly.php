<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle($request, Closure $next)
    {
        // ⭐ CHO PHÉP PUBLIC API STUDENTS
        if (
            $request->is('admin/api/search-students*') ||
            $request->is('admin/api/get-student/*') ||
            $request->is('api/students/search/*') ||
            $request->is('api/students/by-code/*')
        ) {
            return $next($request);
        }

        // ⭐ CHO PHÉP PUBLIC API clubs GET
        if ($request->is('api/clubs*') && $request->method() === 'GET') {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK LOGIN
        |--------------------------------------------------------------------------
        */
        if (!Auth::check()) {
            // Nếu là API → trả JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Chưa đăng nhập'
                ], 401);
            }

            // Nếu là WEB → redirect
            return redirect()->route('login')->with('error', 'Bạn chưa đăng nhập');
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK ADMIN ROLE
        |--------------------------------------------------------------------------
        */
        if (Auth::user()->role_id != 1) {
            // API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Bạn không có quyền'
                ], 403);
            }

            // WEB - Redirect về trang chủ tương ứng với role của user
            // Tránh redirect()->back() vì có thể gây vòng lặp khi đang ở route admin
            $user = Auth::user();
            if ($user->role_id == 2) {
                // Sinh viên → về trang chủ sinh viên
                return redirect()->route('student.home')->with('error', 'Bạn không có quyền truy cập trang quản trị');
            }
            
            // Các role khác → về trang chủ
            return redirect()->route('guest.home')->with('error', 'Bạn không có quyền truy cập');
        }

        return $next($request);
    }
}
