@extends('layouts.admin')

@section('title', 'Danh sách vai trò')

@section('content')

<div class="container-fluid mt-3">

    <h3 class="fw-bold mb-3">Danh sách vai trò hệ thống</h3>

    <div class="table-responsive">
        <table class="table-role table">
            <thead>
                <tr>
                    <th>Tên vai trò</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($roles as $item)
                <tr>

                    <!-- Tên vai trò -->
                    <td>
                        <span class="role-badge role-{{ $item->id }}">
                            {{ $item->name }}
                        </span>
                    </td>

                    <!-- Mô tả tự sinh -->
                    <td>
                        @switch($item->name)
                            @case('Admin')
                                Quản trị toàn bộ hệ thống
                                @break

                            @case('Student')
                                Thành viên sinh viên hệ thống
                                @break

                            @case('Guest')
                                Người dùng khách xem thông tin
                                @break

                            @default
                                —
                        @endswitch
                    </td>

                    <!-- Trạng thái -->
                    <td>
                        <span class="status-dot status-online"></span>
                        <span class="text-success fw-bold">Hoạt động</span>
                    </td>

                    <!-- Ngày tạo -->
                    <td>
                        {{ $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '—' }}
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
