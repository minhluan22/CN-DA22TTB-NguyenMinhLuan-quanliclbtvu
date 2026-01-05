<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\HandlesFiltering;
use Illuminate\Http\Request;

/**
 * Base Controller cho tất cả Admin Controllers
 * Cung cấp các phương thức chung và logic tái sử dụng
 */
abstract class BaseAdminController extends Controller
{
    use HandlesFiltering;

    /**
     * Lấy danh sách CLB active cho filter dropdown
     */
    protected function getActiveClubs()
    {
        return \App\Models\Club::where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    /**
     * Format response cho API hoặc AJAX requests
     */
    protected function jsonResponse($success, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Redirect với success message
     */
    protected function redirectWithSuccess($route, $message)
    {
        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Redirect với error message
     */
    protected function redirectWithError($route, $message)
    {
        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Redirect back với success message
     */
    protected function backWithSuccess($message)
    {
        return back()->with('success', $message);
    }

    /**
     * Redirect back với error message
     */
    protected function backWithError($message)
    {
        return back()->with('error', $message);
    }
}

