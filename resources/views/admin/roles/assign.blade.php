@extends('layouts.admin')

@section('title', 'Gán quyền cho tài khoản')

@section('content')

<div class="container-fluid mt-3">

    <h3 class="fw-bold mb-3">Gán quyền cho tài khoản</h3>
    @if(session('success'))
    <div class="alert alert-success assign-success-box">
        {{ session('success') }}
    </div>
    @endif


    <!-- Hộp quy ước theo theme vàng - xanh -->
    <div class="assign-info-box mb-4">
        <p class="fw-bold mb-1">Quy ước vai trò:</p>
        <p>• <strong>Admin</strong>: Toàn quyền hệ thống</p>
        <p>• <strong>Student</strong>: Sinh viên đã đăng nhập</p>
        <p>• <strong>Guest</strong>: Chỉ được xem thông tin</p>
    </div>

    <!-- Form tìm kiếm -->
    <form method="GET" class="assign-search-row mb-4 d-flex gap-3">
        <input type="text" class="form-control assign-input"
               name="keyword" 
               placeholder="Tìm MSSV / Họ tên / Email..."
               value="{{ request('keyword') }}">

        <select name="role" class="form-select assign-select">
            <option value="">-- Chọn vai trò --</option>
            <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Admin</option>
            <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Student</option>
            <option value="3" {{ request('role') == '3' ? 'selected' : '' }}>Guest</option>
        </select>

        <button class="btn btn-primary assign-btn-search">Tìm</button>
    </form>

    <!-- Bảng -->
    <div class="table-responsive">
        <table class="table assign-table table-role">
            <thead>
                <tr>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Vai trò hiện tại</th>
                    <th>Gán vai trò mới</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
            @foreach ($users as $u)
            <tr id="row-{{ $u->id }}">
                <!-- MSSV -->
                <td>{{ $u->student_code ?? '—' }}</td>

                <!-- Họ tên -->
                <td>{{ $u->name }}</td>

                <!-- Email -->
                <td>{{ $u->email }}</td>

                <!-- Vai trò hiện tại -->
                <td>
                    @php
                        $roleName = match($u->role_id) {
                            1 => 'Admin',
                            2 => 'Student',
                            3 => 'Guest',
                            default => 'Guest'
                        };
                    @endphp

                    <span class="badge role-badge role-{{ $u->role_id }}">
                        {{ $roleName }}
                    </span>
                </td>

                <!-- Gán vai trò mới -->
                <td>
                    <form action="{{ route('admin.assign.update', $u->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @php
                            // Lưu query string và row_id để quay lại đúng vị trí
                            $queryParams = request()->query();
                            $queryParams['row_id'] = $u->id;
                            $backQueryString = http_build_query($queryParams);
                        @endphp
                        <input type="hidden" name="back_query" value="{{ $backQueryString }}">

                        <select name="role" class="form-select assign-select">
                            <option value="Admin"   {{ $roleName=='Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Student" {{ $roleName=='Student' ? 'selected' : '' }}>Student</option>
                            <option value="Guest"   {{ $roleName=='Guest' ? 'selected' : '' }}>Guest</option>
                        </select>
                </td>

                <!-- Nút cập nhật -->
                <td>
                        <button class="btn btn-success assign-btn-update">
                            Cập nhật
                        </button>
                    </form>
                </td>

            </tr>
            @endforeach
            </tbody>



        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $users->links('vendor.pagination.custom') }}
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra xem có row_id trong URL không, nếu có thì scroll đến hàng đó
    const urlParams = new URLSearchParams(window.location.search);
    const rowId = urlParams.get('row_id');
    
    if (rowId) {
        setTimeout(() => {
            const rowElement = document.getElementById('row-' + rowId);
            if (rowElement) {
                rowElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Highlight hàng trong 2 giây
                rowElement.style.transition = 'background-color 0.3s';
                rowElement.style.backgroundColor = '#fff3cd';
                setTimeout(() => {
                    rowElement.style.backgroundColor = '';
                }, 2000);
            }
        }, 300);
        
        // Xóa row_id khỏi URL sau khi scroll (giữ lại các query params khác)
        urlParams.delete('row_id');
        const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.replaceState({}, '', newUrl);
    }
});
</script>
@endpush

@endsection
