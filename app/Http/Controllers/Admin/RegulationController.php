<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Regulation;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegulationController extends BaseAdminController
{
    /**
     * Danh sách nội quy
     */
    public function index(Request $request)
    {
        $query = Regulation::with(['club', 'creator', 'updater']);

        // Áp dụng filters chung
        $query = $this->applyFilters($query, $request, [
            'severity' => ['type' => 'exact', 'column' => 'severity'],
            'status' => ['type' => 'exact', 'column' => 'status'],
        ]);

        // Nếu không có filter status, mặc định hiển thị active
        if (!$request->filled('status')) {
            $query->where('status', 'active');
        }

        // Áp dụng search
        $query = $this->applySearch($query, $request, [
            'code',
            'title',
            'content'
        ]);

        // Pagination
        $regulations = $this->paginateWithQueryString($query, 10, 'issued_date', 'desc');

        return view('admin.regulations.index', compact('regulations'));
    }

    /**
     * Tạo mới nội quy
     */
    public function create()
    {
        $clubs = Club::where('status', 'active')->orderBy('name')->get();
        return view('admin.regulations.create', compact('clubs'));
    }

    /**
     * Lưu nội quy mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:regulations,code',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'scope' => 'required|in:all_clubs,specific_club',
            'club_id' => 'nullable|required_if:scope,specific_club|exists:clubs,id',
            'severity' => 'required|in:light,medium,serious',
            'status' => 'required|in:active,inactive',
            'issued_date' => 'required|date',
        ]);

        Regulation::create([
            'code' => $request->code,
            'title' => $request->title,
            'content' => $request->content,
            'scope' => $request->scope,
            'club_id' => $request->scope == 'specific_club' ? $request->club_id : null,
            'severity' => $request->severity,
            'status' => $request->status,
            'issued_date' => $request->issued_date,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return $this->redirectWithSuccess('admin.regulations.index', 'Tạo nội quy thành công!');
    }

    /**
     * Xem chi tiết nội quy
     */
    public function show($id)
    {
        $regulation = Regulation::with(['club', 'creator', 'updater', 'violations.user', 'violations.club'])
            ->findOrFail($id);
        return view('admin.regulations.show', compact('regulation'));
    }

    /**
     * Chỉnh sửa nội quy
     */
    public function edit($id)
    {
        $regulation = Regulation::findOrFail($id);
        $clubs = Club::where('status', 'active')->orderBy('name')->get();
        return view('admin.regulations.edit', compact('regulation', 'clubs'));
    }

    /**
     * Cập nhật nội quy
     */
    public function update(Request $request, $id)
    {
        $regulation = Regulation::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:50|unique:regulations,code,' . $id,
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'scope' => 'required|in:all_clubs,specific_club',
            'club_id' => 'nullable|required_if:scope,specific_club|exists:clubs,id',
            'severity' => 'required|in:light,medium,serious',
            'status' => 'required|in:active,inactive',
            'issued_date' => 'required|date',
        ]);

        $regulation->update([
            'code' => $request->code,
            'title' => $request->title,
            'content' => $request->content,
            'scope' => $request->scope,
            'club_id' => $request->scope == 'specific_club' ? $request->club_id : null,
            'severity' => $request->severity,
            'status' => $request->status,
            'issued_date' => $request->issued_date,
            'updated_by' => Auth::id(),
        ]);

        return $this->redirectWithSuccess('admin.regulations.index', 'Cập nhật nội quy thành công!');
    }

    /**
     * Bật/tắt trạng thái nội quy
     */
    public function toggleStatus($id)
    {
        $regulation = Regulation::findOrFail($id);
        $regulation->status = $regulation->status == 'active' ? 'inactive' : 'active';
        $regulation->updated_by = Auth::id();
        $regulation->save();

        return $this->backWithSuccess('Cập nhật trạng thái thành công!');
    }
}
