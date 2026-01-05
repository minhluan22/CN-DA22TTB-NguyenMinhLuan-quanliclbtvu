@extends('layouts.admin')

@section('title', 'Danh sách Câu lạc bộ')

@section('content')
<div class="container-fluid mt-3">
    {{-- THÔNG báo thành công --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- THÔNG báo lỗi --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h3 class="fw-bold mb-4">Danh sách Câu lạc bộ</h3>

    {{-- SEARCH & FILTER CARD --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold mb-1">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Mã CLB / Tên CLB / MSSV / Chủ nhiệm..."
                               value="{{ $search }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1">Lĩnh vực</label>
                        <select name="field" class="form-select form-select-sm">
                            <option value="">-- Tất cả lĩnh vực --</option>
                            @foreach(\App\Models\Club::getFieldOptions() as $option)
                                <option value="{{ $option }}" {{ $field == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('admin.clubs.index') }}" class="btn btn-warning btn-sm w-100">
                            <i class="bi bi-arrow-clockwise"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Nút Thêm CLB --}}
    <div class="text-end mb-3">
        <button type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalAddClub">
            <i class="bi bi-plus-circle"></i> Thêm CLB
        </button>
    </div>

    {{-- TABLE CARD --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã CLB</th>
                            <th>Tên CLB</th>
                            <th>Chủ nhiệm</th>
                            <th>Lĩnh vực</th>
                            <th>Thành viên</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($clubs as $club)
                        <tr>
                            <td><strong>{{ $club->code }}</strong></td>
                            <td>{{ $club->name }}</td>
                            <td>
                                @php
                                    // Ưu tiên: club_members (position='chairman') > owner_id > chairman field
                                    $chairmanFromMembers = $chairmenFromMembers[$club->id] ?? null;
                                    $chairmanName = null;
                                    
                                    if ($chairmanFromMembers) {
                                        // Ưu tiên 1: Chủ nhiệm từ club_members
                                        $chairmanName = $chairmanFromMembers->name;
                                    } elseif ($club->owner) {
                                        // Ưu tiên 2: Owner_id (chỉ khi không có chủ nhiệm trong club_members)
                                        $chairmanName = $club->owner->name;
                                    } elseif ($club->chairman) {
                                        // Ưu tiên 3: Trường chairman trong bảng clubs
                                        $chairmanName = preg_replace('/\s*\([^)]*\)\s*$/', '', $club->chairman);
                                    }
                                @endphp
                                @if($chairmanName)
                                    {{ $chairmanName }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ $club->field_display }}
                            </td>
                            <td><span class="badge bg-info">{{ $memberCounts[$club->id] ?? 0 }} thành viên</span></td>

                            <td>
                                @if ($club->status == 'active')
                                    <span class="badge bg-success">✅ Hoạt động</span>
                                @else
                                    <span class="badge bg-danger">🔒 Ngừng hoạt động</span>
                                @endif
                            </td>

                            <td>{{ $club->created_at->format('d/m/Y') }}</td>

                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    {{-- NÚT SỬA CẬP NHẬT CLB --}}
                                    @php
                                        // Logic tương tự như hiển thị: ưu tiên club_members > owner_id > chairman field
                                        $chairmanFromMembers = $chairmenFromMembers[$club->id] ?? null;
                                        $editChairmanName = '';
                                        $editChairmanId = '';
                                        $editMssv = '';
                                        
                                        if ($chairmanFromMembers) {
                                            // Ưu tiên 1: Chủ nhiệm từ club_members
                                            $editChairmanName = $chairmanFromMembers->name . ' (' . $chairmanFromMembers->student_code . ')';
                                            $editChairmanId = $chairmanFromMembers->user_id;
                                            $editMssv = $chairmanFromMembers->student_code;
                                        } elseif ($club->owner) {
                                            // Ưu tiên 2: Owner_id
                                            $editChairmanName = $club->owner->name . ' (' . $club->owner->student_code . ')';
                                            $editChairmanId = $club->owner->id;
                                            $editMssv = $club->owner->student_code;
                                        } elseif ($club->chairman) {
                                            // Ưu tiên 3: Trường chairman
                                            $editChairmanName = $club->chairman;
                                            $editMssv = $club->student_code ?? '';
                                        }
                                    @endphp
                                    <button class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditClub"
                                            onclick="loadClubToEdit('{{ $club->id }}', '{{ addslashes($club->name) }}', '{{ $club->code }}', '{{ addslashes($club->field) }}', '{{ addslashes(\App\Models\Club::getFieldDisplay($club->club_type ?? $club->field)) }}', '{{ $editMssv }}', '{{ addslashes($editChairmanName) }}', '{{ $editChairmanId }}', '{{ $club->status }}', '')"
                                            title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    {{-- BUTTON XÓA --}}
                                    <form action="{{ route('admin.clubs.delete', $club->id) }}"
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Bạn chắc chắn muốn xóa CLB này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger"
                                                title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">Không có dữ liệu</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $clubs->links('vendor.pagination.custom') }}
    </div>
</div>

{{-- ===================== INCLUDE MODALS ===================== --}}
@include('admin.clubs.modal-add')
@include('admin.clubs.modal-edit')

<script>
    // Hàm tải dữ liệu CLB vào modal edit
    function loadClubToEdit(clubId, clubName, clubCode, clubField, clubType, clubMssv, chairmanName, chairmanId, clubStatus) {
        document.getElementById('edit_id').value = clubId;
        document.getElementById('edit_name').value = clubName;
        document.getElementById('edit_code').value = clubCode;
        if (document.getElementById('edit_club_type')) {
            document.getElementById('edit_club_type').value = clubType || '';
        }
        document.getElementById('edit_student_code').value = clubMssv;
        document.getElementById('edit_chairman_input').value = chairmanName;
        document.getElementById('edit_chairman').value = chairmanName;
        var ownerIdEl = document.getElementById('edit_owner_id');
        if (ownerIdEl) ownerIdEl.value = chairmanId || '';
        document.getElementById('edit_status').value = clubStatus || 'active';
        
        // Lấy số trang hiện tại từ URL và set vào hidden input
        var urlParams = new URLSearchParams(window.location.search);
        var currentPage = urlParams.get('page') || '1';
        var pageInput = document.getElementById('edit_page');
        if (pageInput) {
            pageInput.value = currentPage;
            console.log('Set page to:', currentPage); // Debug
        }
        
        // Cập nhật form action URL động
        var editForm = document.getElementById('editClubForm');
        if (editForm) {
            var base = '{{ url('/admin/clubs/update') }}';
            editForm.action = base + '/' + clubId;
        }
    }
    
    // Đảm bảo số trang được set khi modal được mở
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = document.getElementById('modalEditClub');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function() {
                var urlParams = new URLSearchParams(window.location.search);
                var currentPage = urlParams.get('page') || '1';
                var pageInput = document.getElementById('edit_page');
                if (pageInput) {
                    pageInput.value = currentPage;
                    console.log('Modal opened, set page to:', currentPage); // Debug
                }
            });
        }
    });
</script>

@endsection
