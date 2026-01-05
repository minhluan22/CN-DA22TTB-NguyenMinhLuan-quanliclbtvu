@extends('layouts.admin')

@section('title', 'Thống kê hoạt động theo CLB')

@section('content')
<div class="container-fluid mt-3">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-bar-chart"></i> Thống kê hoạt động theo CLB
    </h3>

    {{-- FILTER FORM --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold mb-1">CLB</label>
                        <select name="club_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Tất cả CLB --</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                    {{ $club->code }} - {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Tìm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- STATISTICS TABLE --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>CLB</th>
                            <th>Tổng hoạt động</th>
                            <th>Đang diễn ra</th>
                            <th>Đã kết thúc</th>
                            <th>Đã hủy</th>
                            <th>Bị vô hiệu hóa</th>
                            <th>Tổng lượt tham gia</th>
                            <th>Số SV tham gia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statistics as $index => $stat)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $stat['club']->name }}</strong><br>
                                    <small class="text-muted">{{ $stat['club']->code }}</small>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #0033A0; color: white;">{{ $stat['total_events'] }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #0B3D91; color: white;">{{ $stat['ongoing_events'] }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #5FB84A; color: white;">{{ $stat['finished_events'] }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #FFE600; color: #000;">{{ $stat['cancelled_events'] }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #B84A5F; color: white;">{{ $stat['disabled_events'] }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #0B3D91; color: white;">{{ $stat['total_participations'] }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #8EDC6E; color: #000;">{{ $stat['total_unique_participants'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Không có dữ liệu thống kê
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection