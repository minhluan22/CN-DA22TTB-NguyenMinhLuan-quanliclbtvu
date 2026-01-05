<footer class="student-footer">
    <!-- Dòng 1: Website được thực hiện bởi sinh viên -->
    <div class="footer-credits">
        <p class="credits-main">Website được thực hiện bởi sinh viên Trường Đại học Trà Vinh</p>
        <p class="credits-author">Nguyễn Minh Luân 110122109 DA22TTB</p>
    </div>

    <div class="footer-container">
        <div class="footer-column">
            <h3>CLB ĐẠI HỌC TRÀ VINH</h3>
            <p>Nền tảng quản lý và kết nối các câu lạc bộ sinh viên.</p>
            <p>Phát triển kỹ năng – Kết nối đam mê</p>
        </div>

        <div class="footer-column">
            <h4>Liên kết nhanh</h4>
            <a href="{{ route('student.home') }}">Trang chủ</a>
            <a href="{{ route('student.all-clubs') }}">Câu lạc bộ</a>
            <a href="{{ route('student.activities') }}">Hoạt động</a>
            <a href="{{ route('student.profile') }}">Hồ sơ cá nhân</a>
        </div>

        <div class="footer-column">
            <h4>Liên hệ</h4>
            <p>Email: minhluanngulac@gmail.com</p>
            <p>Hotline: 0123 456 789</p>
            <p>Địa chỉ: Đại Học trà Vinh</p>
        </div>
    </div>

    <div class="footer-bottom">
        © 2025 Hệ thống Quản lý Câu lạc bộ – Trường CLB Đại học Trà Vinh
    </div>
</footer>

<style>
    /* Student Footer Styles - Full width, always at bottom */
    .student-footer {
        background: var(--primary-blue, #0B3D91);
        color: var(--text-light, #ffffff);
        padding-top: 35px;
        margin-top: 40px;
        position: relative;
        z-index: 100;
        box-sizing: border-box;
        transition: margin-left 0.3s ease, width 0.3s ease;
    }

    /* Footer Credits Section - Dòng 1 và 2 */
    .footer-credits {
        width: 100%;
        text-align: center;
        padding: 8px 16px 12px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        margin-bottom: 16px;
    }

    .credits-main {
        font-size: 14px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.85);
        margin: 0 0 4px 0;
        line-height: 1.4;
    }

    .credits-author {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-light, #ffffff);
        margin: 0;
        line-height: 1.4;
    }

    /* Khi sidebar mở - footer thu hẹp bằng với content */
    body:not(.sidebar-closed) .student-footer {
        margin-left: 240px;
        width: calc(100% - 240px);
    }

    /* Khi sidebar đóng - footer full width */
    body.sidebar-closed .student-footer {
        margin-left: 0;
        width: 100%;
    }

    .footer-container {
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 32px;
        padding-bottom: 28px;
    }

    .footer-column h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--accent-yellow, #FFE600);
        letter-spacing: 0.5px;
    }

    .footer-column h4 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 12px;
        color: var(--accent-yellow, #FFE600);
    }

    .footer-column p {
        display: block;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        line-height: 1.8;
    }

    .footer-column a {
        display: block;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        text-decoration: none;
        line-height: 1.8;
        transition: all 0.3s ease;
    }

    .footer-column a:hover {
        color: var(--accent-yellow, #FFE600);
        padding-left: 4px;
    }

    .footer-bottom {
        background: #072d6c;
        text-align: center;
        padding: 14px 20px;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.8);
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        width: 100%;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .student-footer {
            width: 100%;
            margin-left: -16px;
            transform: none;
            left: 0;
            right: 0;
        }

        .footer-credits {
            padding: 6px 12px 10px 12px;
            margin-bottom: 12px;
        }

        .credits-main {
            font-size: 13px;
        }

        .credits-author {
            font-size: 18px;
        }

        .footer-container {
            grid-template-columns: 1fr;
            gap: 24px;
            padding: 0 20px 24px 20px;
            width: calc(100% - 40px);
        }

        .student-footer {
            padding-top: 28px;
        }

        .footer-column h3,
        .footer-column h4 {
            margin-bottom: 10px;
        }
    }
</style>

