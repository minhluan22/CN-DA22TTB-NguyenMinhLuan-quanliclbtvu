@extends('layouts.admin')

@section('title', 'Chi tiết thông báo')

@section('content')

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-bell"></i> Chi tiết thông báo
        </h3>
        <div class="d-flex gap-2">
            @if(isset($supportRequest) && $supportRequest && !$supportRequest->admin_response)
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#respondModal">
                    <i class="bi bi-reply"></i> Trả lời hỗ trợ
                </button>
            @endif
            <a href="{{ route('admin.notifications.inbox') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-4">
                <h4 class="fw-bold">{{ $notification->title }}</h4>
                <div class="text-muted small">
                    <i class="bi bi-person"></i> Người gửi: <strong>{{ $notification->sender->name ?? 'Hệ thống' }}</strong> | 
                    <i class="bi bi-clock"></i> Thời gian: <strong>{{ $notification->sent_at ? $notification->sent_at->format('d/m/Y H:i') : 'Chưa gửi' }}</strong>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Loại thông báo:</label>
                <div>
                    @if($notification->type == 'system')
                        <span class="badge bg-secondary">Thông báo hệ thống</span>
                    @elseif($notification->type == 'regulation')
                        <span class="badge bg-danger">Thông báo nội quy – quy định</span>
                    @elseif($notification->type == 'administrative')
                        <span class="badge bg-primary">Thông báo hành chính</span>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Đối tượng nhận:</label>
                <div>
                    @if($notification->target_type == 'all')
                        <span class="badge bg-primary">Toàn bộ người dùng</span>
                    @elseif($notification->target_type == 'students')
                        <span class="badge bg-info">Tất cả sinh viên</span>
                    @elseif($notification->target_type == 'chairmen')
                        <span class="badge bg-warning">Tất cả Chủ nhiệm CLB</span>
                    @elseif($notification->target_type == 'clubs')
                        <span class="badge bg-success">CLB cụ thể</span>
                        @if($notification->target_ids)
                            <div class="mt-2">
                                @php
                                    $selectedClubs = \App\Models\Club::whereIn('id', $notification->target_ids)->get();
                                @endphp
                                @foreach($selectedClubs as $club)
                                    <span class="badge bg-light text-dark me-1">{{ $club->code }} - {{ $club->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Nội dung:</label>
                <div class="border rounded p-3 bg-light">
                    {!! nl2br(e($notification->body)) !!}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-primary">{{ $notification->total_recipients ?? 0 }}</h5>
                            <p class="text-muted mb-0">Tổng người nhận</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-success">{{ $notification->read_count ?? 0 }}</h5>
                            <p class="text-muted mb-0">Đã đọc</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-warning">{{ $notification->unread_count ?? 0 }}</h5>
                            <p class="text-muted mb-0">Chưa đọc</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($supportRequest) && $supportRequest)
                <div class="alert alert-warning">
                    <strong>ℹ️ Thông tin:</strong> Đây là thông báo từ yêu cầu hỗ trợ. 
                    @if($supportRequest->admin_response)
                        <a href="{{ route('admin.support.show', $supportRequest->id) }}" class="alert-link">Xem chi tiết yêu cầu hỗ trợ</a>
                    @else
                        Bạn có thể trả lời yêu cầu hỗ trợ bằng nút phía trên.
                    @endif
                </div>
            @endif

            <div class="alert alert-info">
                <strong>📌 Lưu ý:</strong> Thông báo đã gửi không thể chỉnh sửa để đảm bảo tính minh bạch.
            </div>
        </div>
    </div>
</div>

<!-- Modal Trả lời hỗ trợ -->
@if(isset($supportRequest) && $supportRequest && !$supportRequest->admin_response)
    <div class="modal fade" id="respondModal" tabindex="-1" aria-labelledby="respondModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="respondModalLabel">
                        <i class="bi bi-reply"></i> Trả lời yêu cầu hỗ trợ
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.support.respond', $supportRequest->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề yêu cầu:</label>
                            <div class="form-control-plaintext">{{ $supportRequest->subject }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung yêu cầu:</label>
                            <div class="border rounded p-3 bg-light" style="max-height: 150px; overflow-y: auto;">
                                {{ $supportRequest->content }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="admin_response" class="form-label fw-bold">
                                <i class="bi bi-chat-text"></i> Nội dung phản hồi <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control" 
                                id="admin_response" 
                                name="admin_response" 
                                rows="6" 
                                required
                                minlength="10"
                                placeholder="Nhập nội dung phản hồi (tối thiểu 10 ký tự)..."
                            >{{ old('admin_response') }}</textarea>
                            @error('admin_response')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">
                                <i class="bi bi-info-circle"></i> Trạng thái <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="open" {{ old('status', $supportRequest->status) == 'open' ? 'selected' : '' }}>Mở</option>
                                <option value="in_progress" {{ old('status', $supportRequest->status) == 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="resolved" {{ old('status', $supportRequest->status) == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                                <option value="closed" {{ old('status', $supportRequest->status) == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                            </select>
                            @error('status')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Hủy
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Gửi phản hồi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection

