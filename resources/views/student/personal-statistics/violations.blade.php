@extends('student.personal-statistics._layout')

@section('violations-content')
<style>
.total-violations-card {
    background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
    color: white;
    text-align: center;
    padding: 40px;
    border-radius: 16px;
    margin-bottom: 24px;
}
.total-violations-card .value {
    font-size: 64px;
    font-weight: 700;
    margin-bottom: 8px;
}
.total-violations-card .label {
    font-size: 18px;
    opacity: 0.9;
}
.no-violations {
    text-align: center;
    padding: 60px 20px;
    background: #dcfce7;
    border-radius: 16px;
    color: #166534;
}
.no-violations i {
    font-size: 64px;
    margin-bottom: 16px;
    display: block;
}
</style>

@if($totalViolations > 0)
    {{-- TOTAL VIOLATIONS CARD --}}
    <div class="total-violations-card">
        <div class="value">{{ $totalViolations }}</div>
        <div class="label">Tổng số vi phạm</div>
    </div>

    {{-- VIOLATIONS BY SEVERITY --}}
    @if(isset($violationsBySeverity) && $violationsBySeverity->count() > 0)
        <div class="stats-grid">
            @foreach($violationsBySeverity as $severity => $count)
                <div class="stat-card">
                    <div class="value">{{ $count }}</div>
                    <div class="label">
                        @if($severity == 'light') Nhẹ
                        @elseif($severity == 'medium') Trung bình
                        @elseif($severity == 'serious') Nghiêm trọng
                        @else {{ $severity }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- FILTER --}}
    <div class="card">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Mức độ</label>
                <select name="severity" class="form-control">
                    <option value="all">-- Tất cả --</option>
                    <option value="light" {{ request('severity') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                    <option value="serious" {{ request('severity') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">CLB</label>
                <select name="club_id" class="form-control">
                    <option value="">-- Tất cả --</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                            {{ $club->code }} - {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Từ ngày</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Đến ngày</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Tìm
                </button>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <h5 class="mb-3">Danh sách vi phạm của bản thân</h5>
        <table class="table-role">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên hoạt động</th>
                    <th>CLB</th>
                    <th>Loại vi phạm</th>
                    <th>Mức độ</th>
                    <th>Trạng thái</th>
                    <th>Ngày phát hiện</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @forelse($violations as $index => $violation)
                    <tr>
                        <td>{{ ($violations->currentPage() - 1) * $violations->perPage() + $index + 1 }}</td>
                        <td><strong>{{ $violation->title }}</strong></td>
                        <td>{{ $violation->club_name }} ({{ $violation->club_code }})</td>
                        <td>{{ $violation->violation_type ?? 'Chưa xác định' }}</td>
                        <td>
                            @if($violation->violation_severity == 'light')
                                <span class="badge badge-warning">Nhẹ</span>
                            @elseif($violation->violation_severity == 'medium')
                                <span class="badge badge-info">Trung bình</span>
                            @elseif($violation->violation_severity == 'serious')
                                <span class="badge badge-danger">Nghiêm trọng</span>
                            @else
                                <span class="badge badge-secondary">Chưa xác định</span>
                            @endif
                        </td>
                        <td>
                            @if($violation->violation_status == 'pending')
                                <span class="badge badge-warning">Chờ xử lý</span>
                            @elseif($violation->violation_status == 'processing')
                                <span class="badge badge-info">Đang xử lý</span>
                            @elseif($violation->violation_status == 'processed')
                                <span class="badge badge-success">Đã xử lý</span>
                            @else
                                <span class="badge badge-secondary">Chưa xác định</span>
                            @endif
                        </td>
                        <td>
                            @if($violation->violation_detected_at)
                                {{ \Carbon\Carbon::parse($violation->violation_detected_at)->format('d/m/Y H:i') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($violation->violation_notes)
                                <small>{{ Str::limit($violation->violation_notes, 50) }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Không có vi phạm nào trong khoảng thời gian này</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $violations->links() }}
        </div>
    </div>
@else
    {{-- NO VIOLATIONS --}}
    <div class="no-violations">
        <i class="bi bi-check-circle"></i>
        <h3>Chúc mừng!</h3>
        <p>Bạn chưa có vi phạm nào. Hãy tiếp tục duy trì tinh thần tham gia tích cực và tuân thủ nội quy CLB.</p>
    </div>
@endif
@endsection
