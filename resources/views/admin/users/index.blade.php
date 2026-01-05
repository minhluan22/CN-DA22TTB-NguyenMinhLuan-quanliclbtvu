@extends('layouts.admin')

@section('title', 'Danh sách tài khoản')

@section('content')
<div class="container-fluid mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h3 class="fw-bold mb-4">Danh sách tài khoản</h3>

    {{-- SEARCH & FILTER CARD --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold mb-1">Tìm kiếm</label>
                        <input type="text" name="keyword"
                               class="form-control form-control-sm"
                               placeholder="MSSV / Họ tên / Email..."
                               value="{{ request('keyword') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1">Vai trò</label>
                        <select name="role_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả vai trò --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }}
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
                        <a href="{{ route('admin.users.index') }}" class="btn btn-warning btn-sm w-100">
                            <i class="bi bi-arrow-clockwise"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- BUTTON ADD --}}
    <div class="text-end mb-3">
        <button type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addModal">
            <i class="bi bi-plus-circle"></i> Thêm tài khoản
        </button>
    </div>

    {{-- TABLE CARD --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>MSSV</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Tình Trạng</th>
                            <th>Trạng Thái</th>
                            <th>Ngày tạo</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td><strong>{{ $u->student_code ?? '—' }}</strong></td>
                            <td>{{ $u->name }}</td>
                            <td class="small">{{ $u->email }}</td>

                            {{--  HIỂN THỊ VAI TRÒ ĐÚNG 100% --}}
                            <td>
                                <span class="badge role-badge role-{{ $u->role_id }}">
                                    {{ $u->role_name }}
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                @if($u->status)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Đã khóa</span>
                                @endif
                            </td>

                            {{-- TRẠNG THÁI (Đăng Nhập/Đăng Xuất) --}}
                            <td>
                                @php
                                    $isOnline = false;
                                    if ($u->last_activity) {
                                        $lastActivity = \Carbon\Carbon::parse($u->last_activity);
                                        $isOnline = $lastActivity->diffInMinutes(now()) <= 5;
                                    }
                                @endphp
                                @if($isOnline)
                                    <span class="badge bg-success">
                                        <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Đăng Nhập
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Đăng Xuất</span>
                                @endif
                            </td>

                            <td>{{ $u->created_at }}</td>

                            {{-- ACTIONS --}}
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">

                                    <button class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal{{ $u->id }}"
                                            title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-secondary btn-reset-mk"
                                            data-id="{{ $u->id }}"
                                            data-name="{{ $u->name }}"
                                            title="Reset mật khẩu">
                                        <i class="bi bi-key"></i>
                                    </button>

                                    <form id="reset-form-{{ $u->id }}"
                                        action="{{ route('admin.users.reset', $u->id) }}"
                                        method="POST" class="d-none">
                                        @csrf
                                    </form>

                                    <button type="button"
                                            class="btn btn-sm btn-toggle-status {{ $u->status ? 'btn-dark' : 'btn-success' }}"
                                            data-id="{{ $u->id }}"
                                            data-name="{{ $u->name }}"
                                            data-status="{{ $u->status }}"
                                            title="{{ $u->status ? 'Khóa' : 'Mở khóa' }}">
                                        <i class="bi {{ $u->status ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                                    </button>

                                    <form id="toggle-form-{{ $u->id }}"
                                        action="{{ route('admin.users.toggle', $u->id) }}"
                                        method="POST" class="d-none">
                                        @csrf
                                    </form>

                                    <button type="button"
                                            class="btn btn-danger btn-sm btn-delete-user"
                                            data-id="{{ $u->id }}"
                                            data-name="{{ $u->name }}"
                                            title="Xóa">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>

                                    <form id="delete-form-{{ $u->id }}"
                                        action="{{ route('admin.users.destroy', $u->id) }}"
                                        method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $users->links('vendor.pagination.custom') }}
    </div>

</div>

{{-- INCLUDE MODALS --}}
@include('admin.users.modal_add')

@foreach($users as $u)
    @include('admin.users.modal_edit', ['user' => $u])
@endforeach

@endsection
