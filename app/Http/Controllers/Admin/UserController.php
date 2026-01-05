<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /** ========== DANH SÁCH TÀI KHOẢN ========== */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? '';
        $role_id = $request->role_id ?? '';

        // Lấy roles cho dropdown
        $roles = Role::all();

        // Query user + eager load role
        $query = User::with('role')->orderBy('id', 'desc');

        if ($keyword !== '') {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('student_code', 'like', "%{$keyword}%");
            });
        }

        if ($role_id !== '') {
            $query->where('role_id', (int)$role_id);
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'roles', 'keyword', 'role_id'));
    }


    /** ========== THÊM TÀI KHOẢN ========== */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'student_code'  => 'required|digits:9|unique:users,student_code',
        ]);

        // Auto tạo email từ MSSV nếu chưa có hoặc không đúng format
        $email = $request->email;
        if (empty($email) || !str_ends_with($email, '@st.tvu.edu.vn')) {
            $email = $request->student_code . '@st.tvu.edu.vn';
        }

        User::create([
            'name'          => $request->name,
            'email'         => $email,
            'student_code'  => $request->student_code,
            'role_id'       => 2,
            'password'      => bcrypt($request->student_code),
            'status'        => 1,
        ]);

        return back()->with('success', 'Tạo tài khoản thành công!');
    }



    /** ========== TRANG SỬA ========== */
    public function edit($id)
    {
        $user  = User::findOrFail($id);
        $roles = Role::where('status',1)->get();

        return view('admin.users.edit', compact('user','roles'));
    }


    /** ========== CẬP NHẬT TÀI KHOẢN ========== */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate đầy đủ
        $request->validate([
            'name'         => 'required|string|max:255',
            'student_code' => 'required|digits:9|unique:users,student_code,' . $user->id,
        ], [
            'name.required'         => 'Vui lòng nhập họ tên.',
            'student_code.required' => 'Vui lòng nhập MSSV.',
            'student_code.digits'   => 'MSSV phải đúng 9 chữ số.',
            'student_code.unique'   => 'MSSV này đã tồn tại.',
        ]);


        // Auto tạo email từ MSSV
        $generatedEmail = $request->student_code . '@st.tvu.edu.vn';

        // Lưu dữ liệu
        $user->name         = $request->name;
        $user->student_code = $request->student_code;
        $user->email        = $generatedEmail;

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Đã cập nhật tài khoản');
    }

    /** ========== KIỂM TRA MSSV TRÙNG REALTIME (AJAX) ========== */
    public function checkMSSV(Request $request)
    {
        $exists = User::where('student_code', $request->student_code)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }


    /** ========== AJAX SEARCH FOR SELECT2 ========== */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $type = $request->get('type', 'mssv'); // 'mssv' or 'chairman'

          $query = User::query();

          // Only return student accounts for MSSV / Chủ nhiệm selection
          // and ensure student_code is present (not null / not empty)
          $query->where('role_id', 2)
              ->whereNotNull('student_code')
              ->where('student_code', '<>', '');

        if ($q !== '') {
            if (ctype_digit($q)) {
                $query->where('student_code', 'like', "%{$q}%");
            } else {
                $query->where('name', 'like', "%{$q}%");
            }
        }

        $users = $query->limit(20)->get();

        $results = $users->map(function ($u) use ($type) {
            if ($type === 'chairman') {
                return [
                    'id' => $u->id,
                    // For chairman dropdown show 'Name (MSSV)'
                    'text' => $u->name . ' (' . $u->student_code . ')'
                ];
            }

            // default: return MSSV as id
            return [
                'id' => $u->student_code,
                'text' => $u->student_code . ' — ' . $u->name
            ];
        });

        return response()->json(['results' => $results]);
    }



    /** ========== XOÁ TÀI KHOẢN ========== */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Đã xoá tài khoản');
    }



    /** ========== RESET PASSWORD ========== */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        // Mật khẩu mới = MSSV
        $newPassword = $user->student_code;

        // Cập nhật mật khẩu
        $user->password = bcrypt($newPassword);
        $user->save();

        return back()->with('success', 'Mật khẩu đã được đặt lại thành MSSV: ' . $newPassword);
    }



    /** ========== BẬT / TẮT TRẠNG THÁI ========== */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status ? 0 : 1;
        $user->save();

        return back()->with('success', 'Đã cập nhật trạng thái');
    }
}
