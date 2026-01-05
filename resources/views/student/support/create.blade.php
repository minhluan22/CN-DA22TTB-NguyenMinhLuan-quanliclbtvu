<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gửi yêu cầu hỗ trợ - Sinh viên</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #0033A0;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF3A0;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
            --secondary: #2b2f3a;
            --card: #ffffff;
            --muted: #6b7280;
            --border: #e5e7eb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--soft-yellow);
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 0;
        }
        
        .body-wrapper {
            display: flex;
            flex: 1;
            margin-top: 0;
            padding-top: 0;
        }


        .content {
            margin-left: 240px;
            padding: 24px;
            padding-top: 24px;
            margin-top: 64px;
            width: calc(100% - 240px);
            max-width: 100%;
            flex: 1;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .header {
            background: var(--card);
            padding: 20px 24px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        .info-box {
            background: linear-gradient(135deg, #E6F0FF 0%, #CCE0FF 100%);
            padding: 20px 24px;
            border-radius: 12px;
            margin-bottom: 32px;
            border-left: 4px solid #0B3D91;
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.1);
        }

        .info-box strong {
            color: #0B3D91;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            font-size: 15px;
            font-weight: 700;
        }

        .info-box .info-row {
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 12px;
            margin-bottom: 8px;
            align-items: center;
        }

        .info-box .info-row:last-child {
            margin-bottom: 0;
        }

        .info-box .info-label {
            font-weight: 600;
            color: #0B3D91;
            font-size: 14px;
        }

        .info-box .info-value {
            color: var(--text-dark);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 28px;
        }

        label {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 14px;
        }

        .required {
            color: #B84A5F;
            font-weight: 700;
        }

        input, textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            background: white;
            color: var(--text-dark);
        }

        input::placeholder, textarea::placeholder {
            color: #9ca3af;
            font-style: italic;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #0B3D91;
            box-shadow: 0 0 0 4px rgba(11, 61, 145, 0.1);
            background: #fafbfc;
        }

        textarea {
            resize: vertical;
            min-height: 140px;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 40px;
            padding-top: 24px;
            border-top: 2px solid #f3f4f6;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
            color: white;
            padding: 14px 32px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 15px;
            flex: 1;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(11, 61, 145, 0.25), 0 2px 4px rgba(11, 61, 145, 0.15);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0033A0 0%, #002280 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(11, 61, 145, 0.35), 0 4px 8px rgba(11, 61, 145, 0.2);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.3);
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 4px 12px rgba(11, 61, 145, 0.25), 0 0 0 4px rgba(11, 61, 145, 0.15);
        }

        .btn-back {
            padding: 8px 16px;
            border: 2px solid #0B3D91;
            color: #0B3D91;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            box-shadow: 0 1px 4px rgba(11, 61, 145, 0.1);
        }

        .btn-back:hover {
            background: #0B3D91;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.2);
        }

        .btn-back:active {
            transform: translateY(0);
        }

        .btn-back:focus {
            outline: none;
            box-shadow: 0 1px 4px rgba(11, 61, 145, 0.1), 0 0 0 3px rgba(11, 61, 145, 0.1);
        }

        .btn-secondary {
            padding: 14px 32px;
            border: 2px solid #0B3D91;
            color: #0B3D91;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 15px;
            background: white;
            flex: 1;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.1);
            position: relative;
        }

        .btn-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: -1;
        }

        .btn-secondary:hover::before {
            width: 100%;
        }

        .btn-secondary:hover {
            color: white;
            border-color: #0033A0;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(11, 61, 145, 0.25);
        }

        .btn-secondary:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.2);
        }

        .btn-secondary:focus {
            outline: none;
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.1), 0 0 0 4px rgba(11, 61, 145, 0.1);
        }

        .alert-error {
            background: #FFF3A0;
            color: #B84A5F;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            border-left: 4px solid #B84A5F;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }

        @media (max-width: 900px) {
            .sidebar { 
                top: 56px;
                height: calc(100vh - 56px);
                width: 280px;
            }
            .content { 
                margin-left: 0;
                padding: 16px;
                width: 100%;
            }
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
            body.sidebar-open .sidebar-overlay {
                display: block;
            }
            .card {
                padding: 24px;
                max-width: 100%;
            }
            .info-box .info-row {
                grid-template-columns: 1fr;
                gap: 4px;
            }
            .btn-group {
                flex-direction: column;
            }
            .btn-primary, .btn-secondary {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @include('student.header')
    

    
    <div class="body-wrapper">
        @include('student.sidebar')

        <main class="content">
            <div class="header">
                <div class="header-title">
                    <i class="bi bi-headset"></i> Gửi yêu cầu hỗ trợ
                </div>
                <a href="{{ route('student.support.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="card">
                <div class="info-box">
                    <strong><i class="bi bi-info-circle"></i> Thông tin của bạn</strong>
                    <div class="info-row">
                        <span class="info-label">Họ tên:</span>
                        <span class="info-value">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">MSSV:</span>
                        <span class="info-value">{{ Auth::user()->student_code }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ Auth::user()->email }}</span>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert-error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('student.support.store') }}" method="POST" id="supportForm">
                    @csrf
                    
                    <div class="form-group">
                        <label>
                            <i class="bi bi-card-heading"></i>
                            Tiêu đề <span class="required">*</span>
                        </label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required 
                               placeholder="Ví dụ: Vấn đề về đăng ký CLB, Thắc mắc về điểm hoạt động...">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="bi bi-file-text"></i>
                            Nội dung <span class="required">*</span>
                        </label>
                        <textarea name="content" rows="8" required 
                                  placeholder="Mô tả chi tiết yêu cầu hỗ trợ của bạn. Hãy cung cấp đầy đủ thông tin để chúng tôi có thể hỗ trợ bạn tốt nhất...">{{ old('content') }}</textarea>
                        <small style="color: var(--muted); font-size: 12px; margin-top: 6px; display: block;">
                            <i class="bi bi-lightbulb"></i> Gợi ý: Mô tả rõ ràng vấn đề bạn gặp phải để nhận được hỗ trợ nhanh chóng nhất.
                        </small>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn-primary">
                            <i class="bi bi-send-fill"></i> Gửi yêu cầu
                        </button>
                        <a href="{{ route('student.support.index') }}" class="btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    @include('student.footer')
</body>
</html>
