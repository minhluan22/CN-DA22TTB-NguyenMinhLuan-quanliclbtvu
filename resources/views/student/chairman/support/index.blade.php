@extends('layouts.chairman')

@section('title', 'Hỗ trợ - Chủ nhiệm CLB')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #FFF3A0;
    }
    
    .dashboard-container {
        padding: 24px;
        max-width: 1600px;
        margin: 0 auto;
    }
    
    /* Page Header */
    .page-header {
        background: white;
        padding: 24px 32px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #0033A0;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    /* Club Info Card */
    .club-info-card {
        background: linear-gradient(135deg, #0033A0 0%, #0B3D91 100%);
        padding: 24px 32px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(0,51,160,0.2);
        color: white;
    }
    
    .club-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
    }
    
    .club-info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .club-info-label {
        font-size: 13px;
        opacity: 0.85;
        font-weight: 500;
    }
    
    .club-info-value {
        font-size: 18px;
        font-weight: 700;
    }
    
    .table-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-open { background: #FFE600; color: #000; }
    .badge-in_progress { background: #0B3D91; color: white; }
    .badge-resolved { background: #5FB84A; color: white; }
    .badge-closed { background: #6b7280; color: white; }
    .badge-high { background: #B84A5F; color: white; }
    .badge-medium { background: #FFE600; color: #000; }
    .badge-low { background: #8EDC6E; color: #000; }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
    }
    
    .empty-state h4 {
        color: #374151;
        margin-bottom: 8px;
    }
    
    .empty-state p {
        color: #6b7280;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-headset"></i>
            Hỗ trợ & Yêu cầu đến Admin
        </h1>
        <a href="{{ route('student.chairman.support.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Gửi yêu cầu mới
        </a>
    </div>

    <!-- Club Info Card -->
    @if($chairmanClub)
        @php
            $club = \App\Models\Club::find($chairmanClub->id);
        @endphp
        <div class="club-info-card">
            <div class="club-info-grid">
                <div class="club-info-item">
                    <span class="club-info-label">Tên Câu lạc bộ</span>
                    <span class="club-info-value">{{ $chairmanClub->name }}</span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Mã CLB</span>
                    <span class="club-info-value">{{ $chairmanClub->code }}</span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Trạng thái</span>
                    <span class="club-info-value">
                        @if($club && $club->status === 'active')
                            ✅ Hoạt động
                        @else
                            🔒 Ngừng hoạt động
                        @endif
                    </span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Vai trò của bạn</span>
                    <span class="club-info-value">Chủ nhiệm CLB</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-card">
        @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>CLB</th>
                            <th>Tiêu đề</th>
                            <th>Mức độ</th>
                            <th>Trạng thái</th>
                            <th>Ngày gửi</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td>
                                    @if($request->club)
                                        <strong>{{ $request->club->code }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($request->club->name, 30) }}</small>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $request->subject }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($request->content, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $request->priority }}">
                                        {{ $request->priority_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $request->status }}">
                                        {{ $request->status_label }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('student.chairman.support.show', $request->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $requests->links('vendor.pagination.custom') }}
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>Bạn chưa có yêu cầu hỗ trợ nào</h4>
                <p>Hãy gửi yêu cầu đầu tiên của bạn</p>
                <a href="{{ route('student.chairman.support.create') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle"></i> Gửi yêu cầu đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
