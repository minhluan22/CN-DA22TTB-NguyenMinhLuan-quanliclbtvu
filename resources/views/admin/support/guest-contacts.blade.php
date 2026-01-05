@extends('layouts.admin')

@section('title', 'Liên hệ từ Guest')

@section('content')
<div class="container-fluid mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h3 class="fw-bold mb-4">
        <i class="bi bi-envelope"></i> Liên hệ từ Guest
    </h3>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold mb-1">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control form-control-sm" 
                               placeholder="Họ tên, email, tiêu đề..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1">Trạng thái</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Mở</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.support.guest-contacts') }}" class="btn btn-warning btn-sm w-100">
                            <i class="bi bi-arrow-clockwise"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body">
            @if($contacts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Tiêu đề</th>
                                <th>Ngày gửi</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                                <tr>
                                    <td><strong>{{ $contact->name }}</strong></td>
                                    <td class="small">{{ $contact->email }}</td>
                                    <td>{{ Str::limit($contact->subject, 50) }}</td>
                                    <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $contact->status == 'open' ? 'warning text-dark' : 'secondary' }}">
                                            {{ $contact->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('admin.support.show', $contact->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Xem
                                            </a>
                                            @if($contact->status == 'open')
                                                <form action="{{ route('admin.support.mark-processed', $contact->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i> Đã xử lý
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6b7280;"></i>
                    <p class="text-muted mt-3 mb-0">Chưa có liên hệ nào từ Guest.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($contacts->count() > 0)
        <div class="d-flex justify-content-center mt-3">
            {{ $contacts->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
@endsection

