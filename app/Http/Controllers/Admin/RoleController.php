<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // Danh sách vai trò
    public function index()
    {
        $roles = Role::where('id', '!=', 3)->orderBy('id', 'asc')->get();
        return view('admin.roles.index', compact('roles'));
    }

    // Trang thêm vai trò
    public function create()
    {
        return view('admin.roles.create');
    }

    // Lưu vai trò mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'description' => 'nullable'
        ]);

        Role::create($request->only(['name', 'description']));

        return redirect()->route('admin.roles.index')->with('success', 'Thêm vai trò thành công!');
    }

    // Trang sửa vai trò
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }

    // Cập nhật vai trò
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'description' => 'nullable'
        ]);

        $role->update($request->only(['name', 'description']));

        return redirect()->route('admin.roles.index')->with('success', 'Cập nhật vai trò thành công!');
    }

    // Xóa vai trò
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Không cho xóa Admin
        if ($role->name === 'Admin') {
            return back()->with('error', 'Không thể xóa vai trò Admin.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Xóa vai trò thành công!');
    }
}
