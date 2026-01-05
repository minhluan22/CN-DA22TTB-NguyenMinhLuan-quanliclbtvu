<footer class="admin-footer">
    <!-- Website được thực hiện bởi sinh viên -->
    <div class="footer-credits">
        <p class="credits-main">Website được thực hiện bởi sinh viên Trường Đại học Trà Vinh</p>
        <p class="credits-author">Nguyễn Minh Luân 110122109 DA22TTB</p>
    </div>
</footer>

<style>
    /* Admin Footer Styles - Chỉ có credits */
    .admin-footer {
        background: var(--primary-blue, #0B3D91);
        color: var(--text-light, #ffffff);
        padding-top: 35px;
        padding-bottom: 35px;
        margin-top: 40px;
        position: relative;
        z-index: 100;
        box-sizing: border-box;
        margin-left: 260px;
        width: calc(100% - 260px);
        transition: margin-left 0.3s ease, width 0.3s ease;
    }

    /* Footer Credits Section - Nổi bật */
    .footer-credits {
        width: 100%;
        text-align: center;
        padding: 20px 16px 24px 16px;
        border-top: 2px solid rgba(255, 230, 0, 0.3);
        border-bottom: 2px solid rgba(255, 230, 0, 0.3);
        margin: 0;
        background: linear-gradient(135deg, rgba(255, 230, 0, 0.1) 0%, rgba(255, 230, 0, 0.05) 100%);
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .footer-credits::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: var(--accent-yellow, #FFE600);
        border-radius: 0 0 3px 3px;
    }

    .footer-credits::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: var(--accent-yellow, #FFE600);
        border-radius: 3px 3px 0 0;
    }

    .credits-main {
        font-size: 16px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.95);
        margin: 0 0 8px 0;
        line-height: 1.5;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        text-align: center;
        width: 100%;
    }

    .credits-author {
        font-size: 24px;
        font-weight: 800;
        color: var(--accent-yellow, #FFE600);
        margin: 0;
        line-height: 1.5;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        letter-spacing: 0.5px;
        text-align: center;
        width: 100%;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .admin-footer {
            width: 100%;
            margin-left: 0;
        }

        .footer-credits {
            padding: 16px 12px 20px 12px;
        }

        .footer-credits::before,
        .footer-credits::after {
            width: 50px;
            height: 2px;
        }

        .credits-main {
            font-size: 14px;
        }

        .credits-author {
            font-size: 20px;
        }
    }
</style>

