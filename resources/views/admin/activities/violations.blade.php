@extends('layouts.admin')

@section('title', 'Danh sách hoạt động vi phạm')

@section('content')

<div class="container-fluid mt-3">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-exclamation-triangle"></i> Danh sách hoạt động vi phạm
        </h3>
    </div>

    {{-- FILTER FORM --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">CLB</label>
                    <select name="club_id" class="form-select">
                    <option value="">-- Tất cả CLB --</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                            {{ $club->code }} - {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">Mức độ</label>
                <select name="severity" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="light" {{ request('severity') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                    <option value="serious" {{ request('severity') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">Trạng thái</label>
                <select name="violation_status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="pending" {{ request('violation_status') == 'pending' ? 'selected' : '' }}>Chưa xử lý</option>
                    <option value="processing" {{ request('violation_status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="processed" {{ request('violation_status') == 'processed' ? 'selected' : '' }}>Đã xử lý</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-bold mb-1">Từ khóa</label>
                <input type="text" name="search" class="form-control form-control-sm" 
                       value="{{ request('search') }}" placeholder="Tìm kiếm theo tên hoạt động, CLB, loại vi phạm...">
            </div>

            <div class="col-md-1">
                <label class="form-label small fw-bold mb-1">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Tìm
                </button>
            </div>
            </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Tên hoạt động</th>
                    <th>CLB</th>
                    <th>Người tạo</th>
                    <th>Thời gian</th>
                    <th>Loại vi phạm</th>
                    <th>Mức độ</th>
                    <th>Trạng thái xử lý</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($violations as $index => $violation)
                    <tr id="row-{{ $violation->id }}">
                        <td>{{ $violations->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $violation->title }}</strong>
                            @if($violation->violation_notes)
                                <br><small class="text-danger">{{ Str::limit($violation->violation_notes, 80) }}</small>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $violation->club_code }}</small><br>
                            {{ $violation->club_name }}
                        </td>
                        <td>
                            <small>{{ $violation->creator_name ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <small>{{ \Carbon\Carbon::parse($violation->start_at)->format('d/m/Y H:i') }}</small>
                            @if($violation->violation_detected_at)
                                <br><small class="text-muted">Phát hiện: {{ \Carbon\Carbon::parse($violation->violation_detected_at)->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        <td>
                            @if($violation->violation_type)
                                <span class="badge" style="background-color: #FFE600; color: #000;">{{ $violation->violation_type }}</span>
                            @else
                                <span class="text-muted">Chưa xác định</span>
                            @endif
                        </td>
                        <td>
                            @if($violation->violation_severity == 'light')
                                <span class="badge" style="background-color: #8EDC6E; color: #000;">Nhẹ</span>
                            @elseif($violation->violation_severity == 'medium')
                                <span class="badge" style="background-color: #FFE600; color: #000;">Trung bình</span>
                            @elseif($violation->violation_severity == 'serious')
                                <span class="badge" style="background-color: #B84A5F; color: white;">Nghiêm trọng</span>
                            @else
                                <span class="text-muted">Chưa xác định</span>
                            @endif
                        </td>
                        <td>
                            @if($violation->violation_status == 'pending')
                                <span class="badge" style="background-color: #FFE600; color: #000;">Chưa xử lý</span>
                            @elseif($violation->violation_status == 'processing')
                                <span class="badge" style="background-color: #0B3D91; color: white;">Đang xử lý</span>
                            @elseif($violation->violation_status == 'processed')
                                <span class="badge" style="background-color: #5FB84A; color: white;">Đã xử lý</span>
                            @else
                                <span class="badge bg-secondary">Chưa có</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-nowrap">
                                <a href="{{ route('admin.activities.show', $violation->id) }}" 
                                   class="btn btn-sm" style="background-color: #0B3D91; color: white;" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @php
                                    // Lưu query string hiện tại để quay lại đúng vị trí
                                    $queryString = request()->getQueryString();
                                    // Thêm row_id vào query string để scroll đến đúng hàng
                                    $queryParams = request()->query();
                                    $queryParams['row_id'] = $violation->id;
                                    $backQueryString = http_build_query($queryParams);
                                    $backUrl = route('admin.activities.violations') . ($backQueryString ? '?' . $backQueryString : '');
                                @endphp
                                <a href="{{ route('admin.activities.show-update-violation', $violation->id) }}?{{ $backQueryString }}" 
                                   class="btn btn-sm" 
                                   style="background-color: #5FB84A; color: white;" 
                                   title="Ghi nhận & cập nhật xử lý">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                {{-- 🔑 NGUYÊN TẮC VÀNG: Chưa xử lý và Đang xử lý = PHẢI CÓ HÀNH ĐỘNG --}}
                                {{-- Logic: Hiển thị nút "Vô hiệu hóa" KHI:
                                     - Trạng thái xử lý CHƯA là "Đã xử lý" (violation_status !== 'processed')
                                     - VÀ chưa bị vô hiệu hóa (status != 'disabled')
                                     ✅ Chưa xử lý (pending/null) → CÓ nút
                                     ✅ Đang xử lý (processing) → CÓ nút  
                                     ❌ Đã xử lý (processed) → KHÔNG có nút --}}
                                @php
                                    $canDisable = ($violation->violation_status === null || $violation->violation_status === 'pending' || $violation->violation_status === 'processing') 
                                                  && $violation->status !== 'disabled';
                                @endphp
                                @if($canDisable)
                                    <a href="{{ route('admin.activities.show-disable', $violation->id) }}?{{ $backQueryString }}" 
                                       class="btn btn-sm" 
                                       style="background-color: #FFE600; color: #000;" 
                                       title="Vô hiệu hóa">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            Không có hoạt động vi phạm nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

            {{-- PAGINATION --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $violations->links('vendor.pagination.custom') }}
            </div>
            </div>
        </div>
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
