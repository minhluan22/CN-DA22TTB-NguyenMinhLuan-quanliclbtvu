@extends('layouts.chairman')

@section('title', 'Phân quyền CLB - Chủ nhiệm')

@push('styles')
<style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #072C6A;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF7B0;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--soft-yellow);
            color: var(--text-dark);
        }
        .table-role {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-role thead {
            background: #eaf2ff;
            color: #0B3D91;
        }
        .table-role thead th {
            background: #eaf2ff !important;
            color: #0B3D91 !important;
            font-weight: 700;
        }
        .submenu {
            margin-left: 20px;
            margin-top: 4px;
        }
        .submenu a {
            font-size: 13px;
            padding: 8px 12px;
        }

        /* =========================================================
           CUSTOM PAGINATION STYLE
           → Style cho phân trang tùy chỉnh (giống y hệt trang Danh sách tài khoản Admin)
        ========================================================= */
        .pagination {
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0;
            list-style: none;
            padding: 0;
        }

        .pagination .page-item {
            margin: 0 2px;
            list-style: none;
        }

        .pagination .page-link {
            color: #0B3D91;
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 6px 12px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.15s ease;
            min-width: 38px;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            line-height: 1.42857143;
            cursor: pointer;
        }

        .pagination .page-link:hover:not(.disabled):not([aria-disabled="true"]) {
            color: white;
            background-color: #0B3D91;
            border-color: #0B3D91;
            text-decoration: none;
        }

        .pagination .page-item.active .page-link {
            color: white;
            background-color: #0B3D91;
            border-color: #0B3D91;
            font-weight: 600;
            cursor: default;
            z-index: 1;
        }

        .pagination .page-item.active .page-link:hover {
            color: white;
            background-color: #0B3D91;
            border-color: #0B3D91;
        }

        .pagination .page-item.disabled .page-link,
        .pagination .page-item.disabled .page-link:hover,
        .pagination .page-item.disabled .page-link:focus {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
        }

        /* Đảm bảo phân trang hiển thị đúng trong container */
        nav[aria-label="Page navigation"] {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        nav[aria-label="Page navigation"] .pagination {
            margin: 0;
        }
    </style>
@endpush

@section('content')
        <h3 class="fw-bold mb-4">Gán chức vụ</h3>

        {{-- THÔNG BÁO --}}
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

        {{-- THÔNG TIN CLB --}}
        <div class="alert alert-info mb-4">
            <strong>CLB:</strong> {{ $club->name }} ({{ $club->code }}) | 
            <strong>Lưu ý:</strong> Bạn không thể thay đổi chức vụ của chính mình hoặc gán chức vụ Chủ nhiệm (chỉ Admin mới được phép)
        </div>

        {{-- BẢNG DANH SÁCH THÀNH VIÊN --}}
        <div class="table-responsive">
            <table class="table table-role">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên thành viên</th>
                        <th>MSSV</th>
                        <th>Email</th>
                        <th>Chức vụ hiện tại</th>
                        <th>Chức vụ mới</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            <td>{{ ($members->currentPage() - 1) * $members->perPage() + $loop->iteration }}</td>
                            <td><strong>{{ $member->name }}</strong></td>
                            <td>{{ $member->student_code ?? '-' }}</td>
                            <td>{{ $member->email }}</td>
                            <td>
                                @if ($member->position == 'chairman')
                                    <span class="badge" style="background-color: #0033A0; color: white;">Chủ nhiệm</span>
                                @elseif ($member->position == 'vice_chairman')
                                    <span class="badge" style="background-color: #FFE600; color: #000;">Phó chủ nhiệm</span>
                                @elseif ($member->position == 'secretary')
                                    <span class="badge" style="background-color: #0B3D91; color: white;">Thư ký CLB</span>
                                @elseif ($member->position == 'head_expertise')
                                    <span class="badge" style="background-color: #5FB84A; color: white;">Trưởng ban Chuyên môn</span>
                                @elseif ($member->position == 'head_media')
                                    <span class="badge" style="background-color: #8EDC6E; color: #000;">Trưởng ban Truyền thông</span>
                                @elseif ($member->position == 'head_events')
                                    <span class="badge" style="background-color: #FFF3A0; color: #000;">Trưởng ban Sự kiện</span>
                                @elseif ($member->position == 'treasurer')
                                    <span class="badge" style="background-color: #0066CC; color: white;">Trưởng ban Tài chính</span>
                                @else
                                    <span class="badge" style="background-color: #6BCB77; color: white;">Thành viên</span>
                                @endif
                            </td>
                            <td>
                                @if ($member->user_id == Auth::id())
                                    <span class="text-muted">-</span>
                                @else
                                    <form action="{{ route('student.chairman.update-position', $member->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <select name="position" class="form-select form-select-sm" style="width: auto; display: inline-block;" onchange="this.form.submit()">
                                            <option value="member" {{ $member->position == 'member' ? 'selected' : '' }}>Thành viên</option>
                                            @php
                                                // Đếm số lượng hiện có, trừ member hiện tại đang được xem
                                                $positionCounts = [
                                                    'vice_chairman' => $members->where('position', 'vice_chairman')->where('id', '!=', $member->id)->count(),
                                                    'secretary' => $members->where('position', 'secretary')->where('id', '!=', $member->id)->count(),
                                                    'head_expertise' => $members->where('position', 'head_expertise')->where('id', '!=', $member->id)->count(),
                                                    'head_media' => $members->where('position', 'head_media')->where('id', '!=', $member->id)->count(),
                                                    'head_events' => $members->where('position', 'head_events')->where('id', '!=', $member->id)->count(),
                                                    'treasurer' => $members->where('position', 'treasurer')->where('id', '!=', $member->id)->count(),
                                                ];
                                                $positionLimits = [
                                                    'vice_chairman' => 2,
                                                    'secretary' => 1,
                                                    'head_expertise' => 1,
                                                    'head_media' => 1,
                                                    'head_events' => 1,
                                                    'treasurer' => 1,
                                                ];
                                            @endphp
                                            <option value="vice_chairman" {{ $member->position == 'vice_chairman' ? 'selected' : '' }} {{ ($member->position != 'vice_chairman' && $positionCounts['vice_chairman'] >= $positionLimits['vice_chairman']) ? 'disabled' : '' }}>
                                                Phó chủ nhiệm{{ ($positionCounts['vice_chairman'] >= $positionLimits['vice_chairman'] && $member->position != 'vice_chairman') ? ' (Đã đủ)' : '' }}
                                            </option>
                                            <option value="secretary" {{ $member->position == 'secretary' ? 'selected' : '' }} {{ ($member->position != 'secretary' && $positionCounts['secretary'] >= $positionLimits['secretary']) ? 'disabled' : '' }}>
                                                Thư ký CLB{{ ($positionCounts['secretary'] >= $positionLimits['secretary'] && $member->position != 'secretary') ? ' (Đã đủ)' : '' }}
                                            </option>
                                            <option value="head_expertise" {{ $member->position == 'head_expertise' ? 'selected' : '' }} {{ ($member->position != 'head_expertise' && $positionCounts['head_expertise'] >= $positionLimits['head_expertise']) ? 'disabled' : '' }}>
                                                Trưởng ban Chuyên môn{{ ($positionCounts['head_expertise'] >= $positionLimits['head_expertise'] && $member->position != 'head_expertise') ? ' (Đã đủ)' : '' }}
                                            </option>
                                            <option value="head_media" {{ $member->position == 'head_media' ? 'selected' : '' }} {{ ($member->position != 'head_media' && $positionCounts['head_media'] >= $positionLimits['head_media']) ? 'disabled' : '' }}>
                                                Trưởng ban Truyền thông{{ ($positionCounts['head_media'] >= $positionLimits['head_media'] && $member->position != 'head_media') ? ' (Đã đủ)' : '' }}
                                            </option>
                                            <option value="head_events" {{ $member->position == 'head_events' ? 'selected' : '' }} {{ ($member->position != 'head_events' && $positionCounts['head_events'] >= $positionLimits['head_events']) ? 'disabled' : '' }}>
                                                Trưởng ban Hoạt động{{ ($positionCounts['head_events'] >= $positionLimits['head_events'] && $member->position != 'head_events') ? ' (Đã đủ)' : '' }}
                                            </option>
                                            <option value="treasurer" {{ $member->position == 'treasurer' ? 'selected' : '' }} {{ ($member->position != 'treasurer' && $positionCounts['treasurer'] >= $positionLimits['treasurer']) ? 'disabled' : '' }}>
                                                Trưởng ban Tài chính{{ ($positionCounts['treasurer'] >= $positionLimits['treasurer'] && $member->position != 'treasurer') ? ' (Đã đủ)' : '' }}
                                            </option>
                                        </select>
                                    </form>
                                @endif
                            </td>
                            <td>
                                @if ($member->user_id == Auth::id())
                                    <span class="text-muted">Bạn</span>
                                @else
                                    <span class="text-success">✓</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Không có thành viên nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PHÂN TRANG --}}
        @if($members->hasPages())
            <div class="mt-3 d-flex justify-content-center">
                {{ $members->links('vendor.pagination.custom') }}
            </div>
        @endif
@endsection

