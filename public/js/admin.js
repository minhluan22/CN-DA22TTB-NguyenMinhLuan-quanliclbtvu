/* ======================================================
   ADMIN.JS - FULL MERGED VERSION (SIDEBAR + USER ACTIONS)
======================================================= */

document.addEventListener("DOMContentLoaded", function () {

    /* ======================================================
       1) SIDEBAR: Auto Active + Auto Open Menu
    ====================================================== */

    // Kiểm tra và mở submenu nếu có active-sub
    const activeSub = document.querySelector(".active-sub");
    if (activeSub) {
        const submenu = activeSub.closest(".submenu");
        if (submenu) {
            submenu.style.display = "block";
            
            const menuParent = submenu?.previousElementSibling;
            if (menuParent && menuParent.classList.contains("menu-item")) {
                menuParent.classList.add("active");
            }
        }

        // Nếu active-sub là trong submenu-child, mở submenu-child và xoay chevron
        const submenuChild = activeSub.closest(".submenu-child");
        if (submenuChild) {
            submenuChild.style.display = "block";
            
            const title = submenuChild.previousElementSibling;
            if (title && title.classList.contains("submenu-title")) {
                title.classList.add("active-sub");
                const chevron = title.querySelector('i.bi-chevron-right');
                if (chevron) {
                    chevron.style.transform = 'translateY(-50%) rotate(90deg)';
                }
            }
        }
    }

    // Nếu link con trùng URL → active luôn
    document.querySelectorAll(".submenu-child a").forEach(link => {
        if (link.href === window.location.href || link.classList.contains("active-sub")) {
            link.classList.add("active-sub");

            const box = link.closest(".submenu-child");
            if (box) box.style.display = "block";

            const title = box?.previousElementSibling;
            if (title && title.classList.contains("submenu-title")) {
                title.classList.add("active-sub");
                const chevron = title.querySelector('i.bi-chevron-right');
                if (chevron) {
                    chevron.style.transform = 'translateY(-50%) rotate(90deg)';
                }
            }

            const menu = box.closest(".submenu");
            if (menu) menu.style.display = "block";

            const parentMenu = menu?.previousElementSibling;
            if (parentMenu && parentMenu.classList.contains("menu-item")) {
                parentMenu.classList.add("active");
            }
        }
    });

    // Kiểm tra submenu-title có active-sub (không phải link)
    document.querySelectorAll(".submenu-title.active-sub").forEach(title => {
        if (!title.href) { // Chỉ xử lý submenu-title không phải link
            const submenu = title.closest(".submenu");
            if (submenu) submenu.style.display = "block";

            const menuParent = submenu?.previousElementSibling;
            if (menuParent && menuParent.classList.contains("menu-item")) {
                menuParent.classList.add("active");
            }

            // Kiểm tra submenu-child của title này
            const nextSibling = title.nextElementSibling;
            if (nextSibling && nextSibling.classList.contains("submenu-child")) {
                nextSibling.style.display = "block";
                const chevron = title.querySelector('i.bi-chevron-right');
                if (chevron) {
                    chevron.style.transform = 'translateY(-50%) rotate(90deg)';
                }
            }
        }
    });

    /* ======================================================
       2) Toggle menu cha
    ====================================================== */
    window.toggleMenu = function (id) {
        const menu = document.getElementById(id);
        const isOpen = menu.style.display === "block";
        
        // Đóng tất cả submenu khác
        document.querySelectorAll('.submenu').forEach(item => {
            if (item.id !== id) {
                item.style.display = 'none';
                // Reset active state của menu-item khác
                const otherMenuParent = item.previousElementSibling;
                if (otherMenuParent && otherMenuParent.classList.contains("menu-item")) {
                    otherMenuParent.classList.remove("active");
                }
            }
        });
        
        // Toggle menu hiện tại
        menu.style.display = isOpen ? "none" : "block";
        
        // Toggle active state của menu-item
        const menuParent = menu.previousElementSibling;
        if (menuParent && menuParent.classList.contains("menu-item")) {
            if (isOpen) {
                menuParent.classList.remove("active");
            } else {
                menuParent.classList.add("active");
            }
        }
    };

    /* ======================================================
       3) Toggle submenu con
    ====================================================== */
    window.toggleSubMenu = function (id) {
        const submenu = document.getElementById(id);
        const isOpen = submenu.style.display === "block";
        
        // Đóng tất cả submenu-child khác trong cùng submenu cha
        const parentSubmenu = submenu.closest(".submenu");
        if (parentSubmenu) {
            parentSubmenu.querySelectorAll('.submenu-child').forEach(item => {
                if (item.id !== id) {
                    item.style.display = 'none';
                    // Reset chevron của các submenu khác
                    const title = item.previousElementSibling;
                    if (title && title.classList.contains('submenu-title')) {
                        const chevron = title.querySelector('i.bi-chevron-right');
                        if (chevron) {
                            chevron.style.transform = 'translateY(-50%)';
                        }
                        title.classList.remove("active-sub");
                    }
                }
            });
        }
        
        // Toggle submenu hiện tại
        submenu.style.display = isOpen ? "none" : "block";
        
        // Toggle chevron và active state
        const title = submenu.previousElementSibling;
        if (title && title.classList.contains("submenu-title")) {
            const chevron = title.querySelector('i.bi-chevron-right');
            if (chevron) {
                if (isOpen) {
                    chevron.style.transform = 'translateY(-50%)';
                    title.classList.remove("active-sub");
                } else {
                    chevron.style.transform = 'translateY(-50%) rotate(90deg)';
                    title.classList.add("active-sub");
                }
            }
        }
    };

    /* ======================================================
       4) Click ra ngoài để đóng submenu
    ====================================================== */
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.sidebar')) {
            document.querySelectorAll('.submenu, .submenu-child').forEach(item => {
                item.style.display = 'none';
            });
        }
    });

    /* ======================================================
       5) Auto update email khi nhập MSSV (Edit modal)
    ====================================================== */
    document.querySelectorAll('.mssv-input').forEach(input => {
        input.addEventListener('input', function () {
            const emailField = document.getElementById(this.dataset.emailTarget);
            if (emailField) {
                emailField.value = this.value.length === 9
                    ? `${this.value}@st.tvu.edu.vn`
                    : "";
            }
        });
    });

    /* ======================================================
       6) SweetAlert Reset mật khẩu
    ====================================================== */
    document.querySelectorAll('.btn-reset-mk').forEach(btn => {
        btn.addEventListener('click', function () {
            Swal.fire({
                title: "Reset mật khẩu?",
                html: `Đặt lại mật khẩu cho <b>${this.dataset.name}</b>?<br>Mật khẩu mới = MSSV`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Đồng ý",
                cancelButtonText: "Hủy"
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById("reset-form-" + this.dataset.id).submit();
                }
            });
        });
    });

    /* ======================================================
       7) Mở lại modal sửa khi lỗi validate
    ====================================================== */
    const modalError = document.getElementById("modal-error-id");
    if (modalError) {
        const modal = document.getElementById("editUserModal" + modalError.value);
        if (modal) new bootstrap.Modal(modal).show();
    }

    /* ======================================================
       8) SweetAlert Khóa / Mở khóa tài khoản
    ====================================================== */
    document.querySelectorAll(".btn-toggle-status").forEach(btn => {
        btn.addEventListener("click", function () {

            const isActive = this.dataset.status == "1";

            Swal.fire({
                title: isActive ? 'Khóa tài khoản?' : 'Mở khóa tài khoản?',
                html: `Bạn có chắc muốn <b>${isActive ? 'KHÓA' : 'MỞ'}</b> tài khoản của <b>${this.dataset.name}</b>?`,
                icon: isActive ? 'warning' : 'info',
                showCancelButton: true,
                confirmButtonText: isActive ? "Khóa" : "Mở",
                cancelButtonText: "Hủy",
                confirmButtonColor: isActive ? "#d33" : "#3085d6"
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById("toggle-form-" + this.dataset.id).submit();
                }
            });

        });
    });

    /* ======================================================
       9) SweetAlert Xóa user
    ====================================================== */
    document.querySelectorAll(".btn-delete-user").forEach(btn => {
        btn.addEventListener("click", function () {
            Swal.fire({
                title: "Xóa tài khoản?",
                html: `Bạn có chắc muốn xóa <b>${this.dataset.name}</b>?<br>Hành động này không thể hoàn tác.`,
                icon: "error",
                showCancelButton: true,
                confirmButtonText: "Xóa ngay",
                cancelButtonText: "Hủy",
                confirmButtonColor: "#d33"
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById("delete-form-" + this.dataset.id).submit();
                }
            });
        });
    });

    /* ======================================================
       10) AUTO EMAIL + CHECK MSSV TRÙNG (Modal thêm)
    ====================================================== */

    const mssvAdd = document.getElementById("mssvCreate");
    const emailAdd = document.getElementById("createEmail");
    const errorMSSV = document.getElementById("mssvError");
    const btnAdd = document.getElementById("btnAddUser");

    if (mssvAdd) {
        mssvAdd.addEventListener("input", function () {
            const mssv = this.value.trim();

            // Auto email
            emailAdd.value = (mssv.length === 9) ? `${mssv}@st.tvu.edu.vn` : "";

            if (mssv.length !== 9) {
                errorMSSV.style.display = "none";
                btnAdd.disabled = false;
                return;
            }

            // AJAX check MSSV tồn tại
            fetch("/admin/users/check-mssv", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ student_code: mssv })
            })
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    errorMSSV.innerText = "❌ MSSV đã tồn tại!";
                    errorMSSV.style.display = "block";
                    btnAdd.disabled = true;
                } else {
                    errorMSSV.style.display = "none";
                    btnAdd.disabled = false;
                }
            });
        });
    }

    /* ======================================================
       11) Mở lại modal thêm khi lỗi validate
    ====================================================== */
    const addError = document.getElementById("add-error-flag");
    if (addError && addError.value === "1") {
        new bootstrap.Modal(document.getElementById("addModal")).show();
    }

    /* ======================================================
       12) Modal Autofocus
    ====================================================== */
    document.addEventListener("shown.bs.modal", function (event) {
        let modal = event.target;
        let firstInput = modal.querySelector("input, select, textarea");
        if (firstInput) firstInput.focus();
    });

});
