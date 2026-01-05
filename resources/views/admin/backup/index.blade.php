@extends('layouts.admin')

@section('title', 'Sao lưu dữ liệu')

@section('content')
<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-database"></i> Sao lưu dữ liệu
        </h3>
        <form action="{{ route('admin.backup.create') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-cloud-download"></i> Tạo backup mới
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- INFO CARD --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-info-circle"></i> Thông tin</h5>
            <p class="card-text mb-0">
                <strong>Database:</strong> {{ config('database.default') }}<br>
                <strong>Lưu ý:</strong> Nên thực hiện sao lưu định kỳ để đảm bảo an toàn dữ liệu. File backup sẽ được lưu trong thư mục storage/app/backups.
            </p>
        </div>
    </div>

    {{-- AUTO BACKUP CONFIG --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-clock-history"></i> Cấu hình sao lưu tự động</h5>
            <form action="{{ route('admin.backup.auto-config') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="daily_enabled" id="daily_enabled" 
                                   {{ $autoBackupConfig['daily_enabled'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="daily_enabled">
                                <strong>Sao lưu hàng ngày</strong><br>
                                <small class="text-muted">Tự động sao lưu lúc 2:00 AM mỗi ngày</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="weekly_enabled" id="weekly_enabled" 
                                   {{ $autoBackupConfig['weekly_enabled'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="weekly_enabled">
                                <strong>Sao lưu hàng tuần</strong><br>
                                <small class="text-muted">Tự động sao lưu Chủ nhật 3:00 AM</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="monthly_enabled" id="monthly_enabled" 
                                   {{ $autoBackupConfig['monthly_enabled'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="monthly_enabled">
                                <strong>Sao lưu hàng tháng</strong><br>
                                <small class="text-muted">Tự động sao lưu ngày 1, 4:00 AM</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu cấu hình
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Tên file</th>
                    <th>Kích thước</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $index => $backup)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <i class="bi bi-file-earmark-zip"></i> 
                            <strong>{{ $backup['filename'] }}</strong>
                        </td>
                        <td>{{ $backup['size_human'] }}</td>
                        <td>{{ $backup['created_at'] }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.backup.download', $backup['filename']) }}" 
                                   class="btn btn-sm btn-success" title="Tải xuống">
                                    <i class="bi bi-download"></i>
                                </a>
                                
                                <form action="{{ route('admin.backup.restore', $backup['filename']) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('⚠️ CẢNH BÁO: Thao tác này sẽ ghi đè dữ liệu hiện tại. Bạn có chắc chắn muốn khôi phục?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning" title="Khôi phục">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.backup.delete', $backup['filename']) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa file backup này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mb-0 mt-2">Chưa có file backup nào</p>
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
