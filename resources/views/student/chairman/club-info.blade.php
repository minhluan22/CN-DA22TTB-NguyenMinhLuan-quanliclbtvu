@extends('layouts.chairman')

@section('title', 'Thông tin CLB - ' . $club->name)

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
        
        :root {
            --primary-blue: #0B3D91;
            --primary-blue-dark: #072C6A;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF9D6;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
            --card: #ffffff;
            --muted: #6b7280;
            --border: #e5e7eb;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            box-sizing: border-box;
        }
        .card-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border);
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 14px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: var(--primary-blue);
            color: white;
        }
        .btn-primary:hover {
            background: var(--primary-blue-dark);
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .stat-item {
            text-align: center;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            color: white;
        }
        .stat-item .value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .stat-item .label {
            font-size: 12px;
            opacity: 0.9;
        }
        .executive-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .executive-item {
            padding: 12px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .executive-info {
            flex: 1;
        }
        .executive-info .name {
            font-weight: 600;
            margin-bottom: 4px;
        }
        .executive-info .meta {
            font-size: 12px;
            color: var(--muted);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 12px;
        }
        .badge.success {
            background: #dcfce7;
            color: #166534;
        }
        .badge.danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge.warning {
            background: var(--soft-yellow);
            color: var(--text-dark);
        }
        .file-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .file-item {
            padding: 12px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }
        .info-row {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-row .label {
            font-weight: 600;
            color: var(--muted);
        }
        .info-row .value {
            color: var(--text-dark);
        }
        .logo-preview, .banner-preview {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid var(--border);
            margin-top: 8px;
        }
        .banner-preview {
            width: 100%;
            height: 200px;
        }
    </style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-info-circle"></i>
            Thông tin CLB
        </h1>
    </div>

    <!-- Club Info Card -->
    @if($chairmanClub)
        @php
            $clubModel = \App\Models\Club::find($chairmanClub->id);
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
                        @if($clubModel && $clubModel->status === 'active')
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
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            ❌ {{ session('error') }}
        </div>
    @endif

        <form action="{{ route('student.chairman.club-info.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="info-grid">
                <!-- KHỐI 1: Thông tin chung -->
                <div class="card">
                    <div class="card-title">📝 Thông tin cơ bản của CLB</div>
                    
                    <div class="form-group">
                        <label>Tên câu lạc bộ *</label>
                        <input type="text" value="{{ $club->name }}" disabled style="background: #f3f4f6;" required>
                        <small style="color: var(--muted); font-size: 12px;">Chỉ Admin mới được sửa tên CLB</small>
                    </div>

                    <div class="form-group">
                        <label>Mã CLB</label>
                        <input type="text" value="{{ $club->code }}" disabled style="background: #f3f4f6;">
                        <small style="color: var(--muted); font-size: 12px;">Mã CLB không thể thay đổi</small>
                    </div>

                    <div class="form-group">
                        <label>Lĩnh Vực Hoạt Động</label>
                        <select name="club_type" class="form-control">
                            <option value="">-- Chọn lĩnh vực --</option>
                            @foreach(\App\Models\Club::getFieldOptions() as $option)
                                <option value="{{ $option }}" {{ old('club_type', $club->field_display ?? '') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Mô tả chi tiết CLB</label>
                        <textarea name="description" rows="5">{{ old('description', $club->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Mục tiêu hoạt động</label>
                        <textarea name="activity_goals" rows="5" placeholder="Nhập mục tiêu hoạt động của CLB...">{{ old('activity_goals', $club->activity_goals) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Ngày thành lập</label>
                        <input type="date" name="establishment_date" value="{{ old('establishment_date', $club->establishment_date) }}">
                    </div>

                    <div class="form-group">
                        <label>Trạng thái CLB *</label>
                        <select name="status" required>
                            <option value="active" {{ old('status', $club->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="pending" {{ old('status', $club->status) == 'pending' ? 'selected' : '' }}>Tạm ngưng</option>
                            <option value="archived" {{ old('status', $club->status) == 'archived' ? 'selected' : '' }}>Bị đình chỉ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Logo CLB</label>
                        @if($club->logo)
                            <img src="{{ asset('storage/' . $club->logo) }}" alt="Logo" class="logo-preview">
                        @endif
                        <input type="file" name="logo" accept="image/*" onchange="previewImage(this, 'logo-preview')">
                        <small style="color: var(--muted); font-size: 12px;">Tối đa 2MB</small>
                    </div>
                </div>

                <!-- KHỐI 2: Liên hệ & thông tin hiển thị -->
                <div class="card">
                    <div class="card-title">📞 Liên hệ & thông tin hiển thị ngoài website</div>
                    
                    <div class="form-group">
                        <label>Email CLB</label>
                        <input type="email" name="email" value="{{ old('email', $club->email) }}" placeholder="club@example.com">
                    </div>

                    <div class="form-group">
                        <label>Fanpage</label>
                        <input type="text" name="fanpage" value="{{ old('fanpage', $club->fanpage) }}" placeholder="https://facebook.com/club">
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại liên hệ</label>
                        <input type="text" name="phone" value="{{ old('phone', $club->phone) }}" placeholder="0123456789">
                    </div>

                    <div class="form-group">
                        <label>Link Facebook Group / Discord / Zalo</label>
                        <textarea name="social_links" rows="3" placeholder="Facebook: https://...&#10;Discord: https://...&#10;Zalo: https://...">{{ old('social_links', $club->social_links) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Nơi sinh hoạt</label>
                        <input type="text" name="meeting_place" value="{{ old('meeting_place', $club->meeting_place) }}" placeholder="Phòng A101, Khu A">
                    </div>

                    <div class="form-group">
                        <label>Lịch sinh hoạt cố định</label>
                        <input type="text" name="meeting_schedule" value="{{ old('meeting_schedule', $club->meeting_schedule) }}" placeholder="Thứ 2, 4, 6 - 18:00-20:00">
                    </div>
                </div>
            </div>

            <!-- KHỐI 3: Ban chủ nhiệm -->
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-title">👑 Ban chủ nhiệm</div>
                
                <div class="executive-list">
                    @if($chairman)
                        <div class="executive-item">
                            <div class="executive-info">
                                <div class="name">👑 Chủ nhiệm: {{ $chairman->name }}</div>
                                <div class="meta">MSSV: {{ $chairman->student_code }} | Email: {{ $chairman->email }}</div>
                            </div>
                            <span class="badge danger">Chỉ Admin thay đổi</span>
                        </div>
                    @endif

                    @if($viceChairmen->count() > 0)
                        @foreach($viceChairmen as $vice)
                            <div class="executive-item">
                                <div class="executive-info">
                                    <div class="name">⭐ Phó Chủ nhiệm: {{ $vice->name }}</div>
                                    <div class="meta">MSSV: {{ $vice->student_code }} | Email: {{ $vice->email }}</div>
                                </div>
                                <a href="{{ route('student.chairman.manage-positions') }}" class="btn btn-primary" style="font-size: 12px; padding: 6px 12px;">Thay đổi</a>
                            </div>
                        @endforeach
                    @else
                        <div class="executive-item">
                            <div class="executive-info">
                                <div class="name" style="color: var(--muted);">Chưa có Phó Chủ nhiệm</div>
                            </div>
                            <a href="{{ route('student.chairman.manage-positions') }}" class="btn btn-primary" style="font-size: 12px; padding: 6px 12px;">Bổ nhiệm</a>
                        </div>
                    @endif

                    @if($executives->count() > 0)
                        @foreach($executives as $exec)
                            <div class="executive-item">
                                <div class="executive-info">
                                    <div class="name">
                                        @if($exec->position === 'secretary') 📝 Thư ký CLB
                                        @elseif($exec->position === 'head_expertise') 🎓 Trưởng ban Chuyên môn
                                        @elseif($exec->position === 'head_media') 📢 Trưởng ban Truyền thông
                                        @elseif($exec->position === 'head_events') 🎉 Trưởng ban Sự kiện
                                        @elseif($exec->position === 'treasurer') 💰 Trưởng ban Tài chính
                                        @endif
                                        : {{ $exec->name }}
                                    </div>
                                    <div class="meta">MSSV: {{ $exec->student_code }} | Email: {{ $exec->email }}</div>
                                </div>
                                <a href="{{ route('student.chairman.manage-positions') }}" class="btn btn-primary" style="font-size: 12px; padding: 6px 12px;">Thay đổi</a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- KHỐI 4: Thống kê & Cài đặt -->
            <div class="info-grid">
                <div class="card">
                    <div class="card-title">📊 Thống kê tổng quan</div>
                    
                    <div class="stat-grid">
                        <div class="stat-item">
                            <div class="value">{{ $totalMembers }}</div>
                            <div class="label">Tổng thành viên</div>
                        </div>
                        <div class="stat-item">
                            <div class="value">{{ $suspendedMembers }}</div>
                            <div class="label">Bị đình chỉ</div>
                        </div>
                        <div class="stat-item">
                            <div class="value">{{ $totalEvents }}</div>
                            <div class="label">Sự kiện đã tổ chức</div>
                        </div>
                        <div class="stat-item">
                            <div class="value">{{ $pendingRegistrations }}</div>
                            <div class="label">Đơn chờ duyệt</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="label">Điểm hoạt động trung bình:</div>
                        <div class="value"><strong>{{ number_format($avgActivityPoints, 1) }} điểm</strong></div>
                    </div>
                </div>


            </div>

            <!-- KHỐI 5: Hồ sơ pháp lý -->
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-title">📄 Hồ sơ pháp lý của CLB</div>
                
                <div class="file-list">
                    @if($clubProposal)
                        <div class="file-item">
                            <div>
                                <div style="font-weight: 600;">Đơn đề nghị thành lập CLB</div>
                                <div style="font-size: 12px; color: var(--muted);">
                                    Ngày gửi: {{ \Carbon\Carbon::parse($clubProposal->created_at)->format('d/m/Y') }} | 
                                    Trạng thái: 
                                    @if($clubProposal->status == 'approved')
                                        <span class="badge success">Đã duyệt</span>
                                    @elseif($clubProposal->status == 'rejected')
                                        <span class="badge danger">Từ chối</span>
                                    @else
                                        <span class="badge warning">Chờ duyệt</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($clubProposal->member_list_file)
                                    <a href="{{ asset('storage/' . $clubProposal->member_list_file) }}" target="_blank" class="btn btn-primary" style="font-size: 12px; padding: 6px 12px;">Tải xuống</a>
                                @endif
                            </div>
                        </div>
                    @else
                        <div style="text-align: center; padding: 20px; color: var(--muted);">
                            Chưa có hồ sơ pháp lý
                        </div>
                    @endif
                </div>
            </div>

            <div style="text-align: right; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
            </div>
        </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input, className) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = document.querySelector('.' + className);
                if (!preview) {
                    preview = document.createElement('img');
                    preview.className = className;
                    input.parentNode.insertBefore(preview, input);
                }
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush

