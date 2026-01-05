<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AssignRoleController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo MSSV, họ tên, Email
        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('student_code', 'like', "%{$keyword}%")
                  ->orWhere('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo vai trò hiện tại
        if ($request->role) {
            $query->where('role_id', $request->role);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // Lấy danh sách roles để hiển thị
        $roles = \App\Models\Role::all();

        return view('admin.roles.assign', compact('users', 'roles'));
    }

        public function update(Request $request, $id)
        {
            $user = User::findOrFail($id);

            // Chuyển role string → role_id
            $roleId = match ($request->role) {
                'Admin' => 1,
                'Student' => 2,
                'Guest' => 3,
                default => 3,
            };

            $user->role_id = $roleId;
            $user->save();

            // Redirect về trang assign với query string để quay lại đúng vị trí
            $queryString = $request->input('back_query');
            $redirectUrl = route('admin.assign.index');
            if ($queryString) {
                $redirectUrl .= '?' . $queryString;
            }
            return redirect($redirectUrl)->with('success', 'Cập nhật vai trò thành công!');
        }

}
