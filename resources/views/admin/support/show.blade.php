@extends('layouts.admin')

@section('title', 'Chi tiết yêu cầu hỗ trợ')

@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header" style="background: #0B3D91; color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-headset"></i> Chi tiết yêu cầu hỗ trợ
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Người gửi:</strong>
                        <p>{{ $request->sender_name }} 
                            @if($request->user)
                                ({{ $request->user->student_code }})
                            @endif
                        </p>
                    </div>

                    @if($request->club)
                        <div class="mb-3">
                            <strong>CLB:</strong>
                            <p>{{ $request->club->name }} ({{ $request->club->code }})</p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Email:</strong>
                        <p>{{ $request->sender_email }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Tiêu đề:</strong>
                        <p class="fs-5">{{ $request->subject }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Nội dung:</strong>
                        <div class="border p-3 rounded" style="background: #f9fafb; white-space: pre-wrap;">
                            {{ $request->content }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Ngày gửi:</strong>
                        <p>{{ $request->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($request->admin_response)
                        <div class="alert alert-info">
                            <strong>Phản hồi từ Admin:</strong>
                            <p class="mb-0 mt-2" style="white-space: pre-wrap;">{{ $request->admin_response }}</p>
                            @if($request->responded_at)
                                <small class="text-muted">
                                    Phản hồi lúc: {{ $request->responded_at->format('d/m/Y H:i') }}
                                    bởi {{ $request->responder->name ?? 'Admin' }}
                                </small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header" style="background: #0B3D91; color: white;">
                    <h5 class="mb-0">Thông tin</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Loại:</strong>
                        <p>
                            @if($request->sender_type == 'guest')
                                <span class="badge bg-secondary">Guest</span>
                            @elseif($request->sender_type == 'student')
                                <span class="badge bg-info">Sinh viên</span>
                            @else
                                <span class="badge bg-warning">Chủ nhiệm CLB</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <strong>Trạng thái:</strong>
                        <p>
                            <span class="badge bg-{{ $request->status == 'open' ? 'warning' : ($request->status == 'resolved' ? 'success' : 'secondary') }}">
                                {{ $request->status_label }}
                            </span>
                        </p>
                    </div>

                    @if($request->priority)
                        <div class="mb-3">
                            <strong>Mức độ ưu tiên:</strong>
                            <p>
                                <span class="badge bg-{{ $request->priority == 'high' ? 'danger' : ($request->priority == 'medium' ? 'warning' : 'info') }}">
                                    {{ $request->priority_label }}
                                </span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="background: #0B3D91; color: white;">
                    <h5 class="mb-0">Phản hồi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.support.respond', $request->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Phản hồi <span class="text-danger">*</span></label>
                            <textarea name="admin_response" class="form-control" rows="6" required
                                      placeholder="Nhập phản hồi...">{{ old('admin_response', $request->admin_response) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="open" {{ $request->status == 'open' ? 'selected' : '' }}>Mở</option>
                                <option value="in_progress" {{ $request->status == 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="resolved" {{ $request->status == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                                <option value="closed" {{ $request->status == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                            </select>
                        </div>
                        <button type="submit" class="btn w-100" style="background: #0B3D91; color: white;">
                            <i class="bi bi-send"></i> Gửi phản hồi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

