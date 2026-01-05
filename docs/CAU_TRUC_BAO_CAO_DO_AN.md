# CẤU TRÚC BÁO CÁO ĐỒ ÁN / THỰC TẬP CHUYÊN NGÀNH
## HỆ THỐNG QUẢN LÝ CÂU LẠC BỘ SINH VIÊN

---

## TRANG BÌA

**Trường Đại học Trà Vinh**

**Khoa Công nghệ Thông tin**

**Tên đề tài:** Hệ thống quản lý câu lạc bộ sinh viên

**Sinh viên thực hiện:** [Họ tên sinh viên]

**MSSV – Lớp:** [MSSV] – [Lớp]

**Giảng viên hướng dẫn:** [Họ tên giảng viên]

**Thời gian thực hiện:** [Tháng/Năm] – [Tháng/Năm]

---

## NHẬN XÉT CỦA GIẢNG VIÊN HƯỚNG DẪN

---

## LỜI CAM ĐOAN

---

## LỜI CẢM ƠN

---

## TÓM TẮT CHUYÊN NGÀNH

---

## MỤC LỤC

---

## DANH MỤC HÌNH ẢNH

---

## DANH MỤC BẢNG BIỂU

---

## DANH MỤC TỪ VIẾT TẮT

- **CLB:** Câu lạc bộ
- **CN:** Chủ nhiệm
- **MSSV:** Mã số sinh viên
- **ERD:** Entity Relationship Diagram
- **MVC:** Model-View-Controller
- **API:** Application Programming Interface
- **CRUD:** Create, Read, Update, Delete
- **PDF:** Portable Document Format
- **CSV:** Comma-Separated Values
- **UI/UX:** User Interface/User Experience

---

## MỞ ĐẦU

### Lý do chọn đề tài

- Nhu cầu quản lý câu lạc bộ sinh viên một cách hiệu quả và chuyên nghiệp
- Khó khăn trong việc quản lý thủ công các hoạt động, thành viên, và tài chính
- Cần hệ thống hỗ trợ quản lý nội quy, vi phạm và kỷ luật
- Tăng cường tính minh bạch và công khai trong hoạt động CLB
- Ứng dụng công nghệ thông tin vào quản lý giáo dục

### Mục đích nghiên cứu

- Xây dựng hệ thống quản lý CLB sinh viên toàn diện
- Tự động hóa quy trình quản lý thành viên, hoạt động, và tài chính
- Hỗ trợ quản lý nội quy và xử lý vi phạm kỷ luật
- Cung cấp công cụ thống kê và báo cáo cho nhà quản lý
- Tạo môi trường tương tác tốt giữa thành viên và ban quản lý CLB

### Đối tượng và phạm vi nghiên cứu

**Đối tượng nghiên cứu:**
- Hệ thống quản lý câu lạc bộ sinh viên
- Quy trình quản lý CLB hiện tại tại Trường Đại học Trà Vinh

**Phạm vi nghiên cứu:**
- Quản lý thông tin CLB và thành viên
- Quản lý hoạt động và đề xuất hoạt động
- Quản lý nội quy, vi phạm và kỷ luật
- Thống kê và báo cáo
- Phân quyền người dùng (Admin, Chủ nhiệm, Thành viên)

---

## CHƯƠNG 1: TỔNG QUAN

### 1.1. Tổng quan về bài toán quản lý câu lạc bộ

#### 1.1.1. Thực trạng quản lý CLB hiện tại

- Quản lý thủ công bằng sổ sách, Excel
- Khó khăn trong việc theo dõi thành viên và hoạt động
- Thiếu công cụ thống kê và báo cáo
- Khó quản lý nội quy và vi phạm
- Thiếu tính minh bạch trong hoạt động

#### 1.1.2. Nhu cầu thực tế

- Hệ thống quản lý tập trung
- Tự động hóa quy trình phê duyệt
- Theo dõi và thống kê hoạt động
- Quản lý nội quy và kỷ luật
- Giao diện thân thiện, dễ sử dụng

### 1.2. Mục tiêu và định hướng giải quyết

#### 1.2.1. Mục tiêu chính

- Xây dựng hệ thống web quản lý CLB toàn diện
- Phân quyền rõ ràng cho từng đối tượng sử dụng
- Tự động hóa các quy trình quản lý
- Cung cấp công cụ thống kê và báo cáo

#### 1.2.2. Định hướng giải quyết

- Sử dụng mô hình MVC
- Phân quyền theo vai trò (Role-Based Access Control)
- Giao diện responsive, thân thiện
- Bảo mật thông tin người dùng

### 1.3. Công nghệ và phương pháp sử dụng

#### 1.3.1. Công nghệ backend

- **Framework:** Laravel 12
- **Ngôn ngữ:** PHP 8.2+
- **Cơ sở dữ liệu:** MySQL
- **ORM:** Eloquent

#### 1.3.2. Công nghệ frontend

- **Template Engine:** Blade
- **CSS Framework:** Bootstrap 5
- **Icons:** Bootstrap Icons
- **JavaScript:** Vanilla JS, jQuery (nếu cần)

#### 1.3.3. Công cụ phát triển

- **IDE:** Visual Studio Code / PhpStorm
- **Version Control:** Git
- **Server:** XAMPP / Laravel Sail
- **PDF Export:** Barryvdh\DomPDF
- **File Storage:** Laravel Storage

#### 1.3.4. Phương pháp phát triển

- **Mô hình:** MVC (Model-View-Controller)
- **Phương pháp:** Agile/Iterative
- **Kiến trúc:** Layered Architecture

---

## CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG

### 2.1. Khảo sát hiện trạng quản lý câu lạc bộ

#### 2.1.1. Thực trạng quản lý hiện nay

- Quản lý thông tin CLB bằng file Word/Excel
- Quản lý thành viên bằng danh sách in ra giấy
- Theo dõi hoạt động bằng sổ ghi chép
- Phê duyệt đơn đăng ký qua email/giấy tờ
- Thống kê thủ công, mất nhiều thời gian
- Khó quản lý nội quy và vi phạm

#### 2.1.2. Những khó khăn và hạn chế

- **Thiếu tập trung:** Dữ liệu phân tán ở nhiều nơi
- **Khó tìm kiếm:** Mất nhiều thời gian để tìm thông tin
- **Thiếu thống kê:** Khó đánh giá hiệu quả hoạt động
- **Không minh bạch:** Thành viên khó theo dõi hoạt động CLB
- **Quy trình phức tạp:** Phê duyệt thủ công, dễ sai sót
- **Khó quản lý kỷ luật:** Thiếu hệ thống theo dõi vi phạm

### 2.2. Phân tích yêu cầu hệ thống

#### 2.2.1. Các đối tượng sử dụng hệ thống

**A. Admin (Quản trị viên hệ thống)**
- Quản lý toàn bộ hệ thống
- Phê duyệt CLB mới
- Quản lý tài khoản người dùng
- Xử lý kỷ luật cấp trường
- Xem thống kê tổng hợp

**B. Chủ nhiệm CLB (Chairman)**
- Quản lý CLB của mình
- Quản lý thành viên CLB
- Tạo và duyệt hoạt động
- Duyệt đơn đăng ký tham gia CLB
- Duyệt đề xuất hoạt động từ thành viên
- Ghi nhận vi phạm
- Xem thống kê CLB

**C. Phó Chủ nhiệm (Vice Chairman)**
- Hỗ trợ Chủ nhiệm quản lý CLB
- Quyền tương tự Chủ nhiệm (có thể giới hạn)

**D. Thư ký, Trưởng ban (Secretary, Leaders)**
- Xem thông tin CLB
- Hỗ trợ quản lý hoạt động (tùy phân quyền)

**E. Thành viên CLB (Member)**
- Xem thông tin CLB
- Đăng ký tham gia hoạt động
- Đề xuất hoạt động mới
- Xem điểm hoạt động
- Xem thông báo

**F. Sinh viên chưa tham gia CLB**
- Xem danh sách CLB
- Đăng ký tham gia CLB
- Xem thông tin công khai của CLB

#### 2.2.2. Yêu cầu chức năng

**A. Quản lý tài khoản và phân quyền**
- Đăng nhập, đăng xuất
- Đăng ký tài khoản (sinh viên)
- Phân quyền theo vai trò
- Quản lý hồ sơ cá nhân

**B. Quản lý CLB**
- Tạo CLB mới (đề xuất)
- Phê duyệt/từ chối CLB
- Cập nhật thông tin CLB
- Quản lý logo, banner CLB
- Quản lý trạng thái CLB (active, pending, archived)

**C. Quản lý thành viên**
- Đăng ký tham gia CLB
- Phê duyệt/từ chối thành viên
- Quản lý chức vụ (Chủ nhiệm, Phó CN, Thư ký, Trưởng ban...)
- Xem danh sách thành viên
- Đình chỉ/Kích hoạt lại thành viên

**D. Quản lý hoạt động**
- Tạo hoạt động mới (Chủ nhiệm)
- Đề xuất hoạt động (Thành viên)
- Duyệt đề xuất hoạt động
- Đăng ký tham gia hoạt động
- Duyệt đăng ký tham gia
- Quản lý điểm hoạt động
- Xem lịch sử hoạt động

**E. Quản lý nội quy và vi phạm**
- Tạo và quản lý nội quy (Admin)
- Ghi nhận vi phạm (Chủ nhiệm)
- Xử lý kỷ luật (Admin)
- Xem lịch sử kỷ luật
- Thống kê vi phạm

**F. Thống kê và báo cáo**
- Thống kê thành viên theo CLB
- Thống kê hoạt động
- Thống kê vi phạm
- Xuất báo cáo Excel/PDF
- Biểu đồ thống kê

**G. Thông báo**
- Gửi thông báo hệ thống
- Thông báo cho thành viên CLB
- Thông báo phê duyệt/từ chối
- Lịch sử thông báo

#### 2.2.3. Yêu cầu phi chức năng

**A. Hiệu năng**
- Thời gian phản hồi < 2 giây
- Hỗ trợ đồng thời ít nhất 100 người dùng
- Tối ưu truy vấn database

**B. Bảo mật**
- Mã hóa mật khẩu (bcrypt)
- Xác thực người dùng (Authentication)
- Phân quyền truy cập (Authorization)
- Bảo vệ CSRF
- Validate dữ liệu đầu vào

**C. Giao diện**
- Responsive design (mobile, tablet, desktop)
- Giao diện thân thiện, dễ sử dụng
- Hỗ trợ tiếng Việt đầy đủ
- Tương thích trình duyệt phổ biến

**D. Khả năng mở rộng**
- Dễ dàng thêm chức năng mới
- Cấu trúc code rõ ràng, dễ bảo trì
- Sử dụng design patterns

**E. Độ tin cậy**
- Xử lý lỗi tốt
- Backup dữ liệu định kỳ
- Logging các thao tác quan trọng

### 2.3. Biểu đồ Use Case hệ thống

#### 2.3.1. Use Case tổng quát

*(Xem file `DIAGRAMS_USE_CASE.md` - Mục 1)*

- Sơ đồ tổng quan các actor và use case chính
- Mối quan hệ giữa Admin, Sinh viên - Quản lý CLB, Sinh viên - Thành viên

#### 2.3.2. Use Case cho Admin

*(Xem file `DIAGRAMS_USE_CASE.md` - Mục 2)*

**Các nhóm chức năng:**
- Quản trị hệ thống (Quản lý tài khoản, CLB, thành viên)
- Quản lý hoạt động (Xem, thống kê, xuất báo cáo)
- Nội quy - Vi phạm (Quản lý nội quy, xử lý kỷ luật)
- Thống kê - Báo cáo
- Thông báo hệ thống

#### 2.3.3. Use Case cho Chủ nhiệm CLB

*(Xem file `DIAGRAMS_USE_CASE.md` - Mục 3)*

**Các nhóm chức năng:**
- Quản lý thành viên CLB
- Quản lý hoạt động CLB
- Duyệt đơn đăng ký và đề xuất
- Quản lý điểm hoạt động
- Nội quy - Vi phạm (Xem, ghi nhận)
- Lịch sử kỷ luật
- Thống kê CLB

#### 2.3.4. Use Case cho Thành viên

*(Xem file `DIAGRAMS_USE_CASE.md` - Mục 4)*

**Các nhóm chức năng:**
- Tìm kiếm và đăng ký CLB
- Xem thông tin CLB
- Đăng ký tham gia hoạt động
- Đề xuất hoạt động mới
- Xem điểm hoạt động
- Xem thông báo
- Quản lý hồ sơ cá nhân

### 2.4. Sơ đồ chức năng hệ thống

#### 2.4.1. Sơ đồ phân rã chức năng

```
HỆ THỐNG QUẢN LÝ CLB
│
├── QUẢN TRỊ HỆ THỐNG
│   ├── Quản lý tài khoản
│   ├── Quản lý CLB
│   ├── Quản lý thành viên
│   └── Cấu hình hệ thống
│
├── QUẢN LÝ HOẠT ĐỘNG
│   ├── Danh sách hoạt động
│   ├── Hoạt động vi phạm
│   └── Thống kê hoạt động
│
├── NỘI QUY - VI PHẠM
│   ├── Quản lý nội quy
│   ├── Danh sách vi phạm
│   ├── Xử lý kỷ luật
│   └── Lịch sử kỷ luật
│
├── THỐNG KÊ - BÁO CÁO
│   ├── Thống kê hoạt động
│   ├── Báo cáo tài chính
│   └── Xuất dữ liệu
│
└── THÔNG BÁO HỆ THỐNG
    ├── Gửi thông báo
    └── Lịch sử thông báo
```

#### 2.4.2. Mô tả các chức năng chính

**A. Quản lý CLB**
- Tạo mới, chỉnh sửa, xóa CLB
- Phê duyệt/từ chối CLB
- Quản lý thông tin CLB (logo, banner, mô tả...)
- Quản lý trạng thái CLB

**B. Quản lý thành viên**
- Đăng ký tham gia CLB
- Phê duyệt thành viên
- Quản lý chức vụ
- Đình chỉ/Kích hoạt thành viên

**C. Quản lý hoạt động**
- Tạo hoạt động
- Đề xuất hoạt động
- Duyệt hoạt động
- Đăng ký tham gia
- Quản lý điểm hoạt động

**D. Quản lý nội quy - Vi phạm**
- Tạo và quản lý nội quy
- Ghi nhận vi phạm
- Xử lý kỷ luật
- Lịch sử kỷ luật

**E. Thống kê và báo cáo**
- Thống kê theo CLB, thời gian
- Xuất báo cáo Excel/PDF
- Biểu đồ thống kê

### 2.5. Thiết kế cơ sở dữ liệu

#### 2.5.1. Mô hình ERD

*(Cần vẽ sơ đồ ERD chi tiết)*

**Các thực thể chính:**
- Users (Người dùng)
- Clubs (Câu lạc bộ)
- Club_Members (Thành viên CLB)
- Events (Hoạt động)
- Event_Registrations (Đăng ký hoạt động)
- Regulations (Nội quy)
- Violations (Vi phạm)
- Notifications (Thông báo)
- Roles (Vai trò)

#### 2.5.2. Mô tả các bảng dữ liệu

**A. Bảng `users`**
- Lưu thông tin người dùng
- Các trường: id, name, email, password, student_code, class, phone, avatar, status...

**B. Bảng `clubs`**
- Lưu thông tin CLB
- Các trường: id, name, slug, code, description, logo, banner, owner_id, status, field, club_type...

**C. Bảng `club_members`**
- Lưu thông tin thành viên CLB
- Các trường: id, user_id, club_id, position, status, joined_at...

**D. Bảng `events`**
- Lưu thông tin hoạt động
- Các trường: id, club_id, title, description, activity_type, start_at, end_at, location, approval_status, status...

**E. Bảng `event_registrations`**
- Lưu thông tin đăng ký tham gia hoạt động
- Các trường: id, event_id, user_id, status, registered_at...

**F. Bảng `regulations`**
- Lưu thông tin nội quy
- Các trường: id, code, title, content, scope, club_id, severity, status, issued_date...

**G. Bảng `violations`**
- Lưu thông tin vi phạm
- Các trường: id, user_id, club_id, regulation_id, description, severity, violation_date, status, discipline_type...

**H. Bảng `notifications`**
- Lưu thông tin thông báo
- Các trường: id, user_id, title, content, type, read_at...

**I. Bảng `roles`**
- Lưu thông tin vai trò
- Các trường: id, name, description...

#### 2.5.3. Quan hệ giữa các bảng

- **Users** ↔ **Clubs:** Một User có thể sở hữu nhiều Clubs (owner_id)
- **Users** ↔ **Club_Members:** Nhiều-nhiều (User có thể tham gia nhiều CLB)
- **Clubs** ↔ **Events:** Một-nhiều (Một CLB có nhiều hoạt động)
- **Users** ↔ **Events:** Nhiều-nhiều qua `event_registrations`
- **Users** ↔ **Violations:** Một-nhiều (Một User có thể có nhiều vi phạm)
- **Regulations** ↔ **Violations:** Một-nhiều (Một nội quy có thể bị vi phạm nhiều lần)
- **Clubs** ↔ **Regulations:** Một-nhiều (Một CLB có nhiều nội quy)

### 2.6. Thiết kế giao diện hệ thống

#### 2.6.1. Giao diện đăng nhập – đăng ký

- Form đăng nhập (email/mã sinh viên, mật khẩu)
- Form đăng ký (thông tin cá nhân, xác thực OTP)
- Quên mật khẩu
- Responsive design

#### 2.6.2. Giao diện quản lý câu lạc bộ

**A. Admin:**
- Danh sách CLB (lọc, tìm kiếm)
- Chi tiết CLB
- Form tạo/chỉnh sửa CLB
- Phê duyệt/từ chối CLB

**B. Chủ nhiệm:**
- Thông tin CLB của mình
- Form cập nhật thông tin CLB
- Quản lý logo, banner

#### 2.6.3. Giao diện quản lý thành viên

**A. Admin:**
- Danh sách thành viên toàn hệ thống
- Lọc theo CLB, trạng thái

**B. Chủ nhiệm:**
- Danh sách thành viên CLB
- Phê duyệt đơn đăng ký
- Quản lý chức vụ
- Đình chỉ/Kích hoạt thành viên

#### 2.6.4. Giao diện quản lý hoạt động

**A. Admin:**
- Danh sách hoạt động (bao gồm đề xuất)
- Lọc theo CLB, trạng thái
- Xem chi tiết hoạt động
- Xuất báo cáo

**B. Chủ nhiệm:**
- Danh sách hoạt động CLB
- Form tạo hoạt động
- Duyệt đề xuất hoạt động
- Duyệt đăng ký tham gia
- Quản lý điểm hoạt động

**C. Thành viên:**
- Xem danh sách hoạt động
- Đăng ký tham gia hoạt động
- Form đề xuất hoạt động
- Xem điểm hoạt động

#### 2.6.5. Giao diện quản lý nội quy - Vi phạm

**A. Admin:**
- Danh sách nội quy
- Form tạo/chỉnh sửa nội quy
- Danh sách vi phạm
- Form xử lý kỷ luật
- Lịch sử kỷ luật (theo thành viên, theo thời gian)
- Xuất báo cáo

**B. Chủ nhiệm:**
- Xem danh sách nội quy
- Danh sách vi phạm CLB
- Form ghi nhận vi phạm
- Lịch sử kỷ luật

#### 2.6.6. Giao diện thống kê - Báo cáo

- Dashboard với các chỉ số tổng quan
- Biểu đồ thống kê (Chart.js hoặc tương tự)
- Bộ lọc thống kê (theo CLB, thời gian)
- Nút xuất Excel/PDF

#### 2.6.7. Giao diện thông báo

- Danh sách thông báo
- Đánh dấu đã đọc
- Thông báo real-time (nếu có)

---

## CHƯƠNG 3: XÂY DỰNG VÀ TRIỂN KHAI HỆ THỐNG

### 3.1. Môi trường và công cụ phát triển

#### 3.1.1. Phần mềm sử dụng

- **XAMPP:** Apache, MySQL, PHP
- **Composer:** Quản lý dependencies
- **Git:** Version control
- **Visual Studio Code:** Code editor
- **phpMyAdmin:** Quản lý database
- **Postman:** Test API (nếu có)

#### 3.1.2. Ngôn ngữ và framework

- **Backend:**
  - PHP 8.2+
  - Laravel 12
  - Eloquent ORM
  - Blade Template Engine

- **Frontend:**
  - HTML5, CSS3
  - JavaScript (Vanilla JS)
  - Bootstrap 5
  - Bootstrap Icons

- **Database:**
  - MySQL 8.0+

- **Libraries:**
  - Barryvdh\DomPDF (Xuất PDF)
  - Laravel Storage (Quản lý file)

### 3.2. Xây dựng chức năng quản lý người dùng

#### 3.2.1. Đăng nhập, đăng xuất

**Chức năng:**
- Form đăng nhập (email/mã sinh viên + mật khẩu)
- Xác thực người dùng
- Lưu session
- Middleware kiểm tra đăng nhập
- Đăng xuất và xóa session

**File liên quan:**
- `app/Http/Controllers/AuthController.php`
- `resources/views/auth/login.blade.php`
- `routes/web.php`

#### 3.2.2. Phân quyền người dùng

**Chức năng:**
- Hệ thống vai trò (Roles)
- Middleware kiểm tra quyền
- Phân quyền theo route
- Phân quyền trong view

**File liên quan:**
- `app/Http/Middleware/AdminOnly.php`
- `app/Models/Role.php`
- `database/migrations/0001_01_01_000000_create_roles_table.php`

### 3.3. Xây dựng chức năng quản lý câu lạc bộ

#### 3.3.1. Tạo, chỉnh sửa, xóa CLB

**Chức năng Admin:**
- Danh sách CLB (lọc, tìm kiếm, phân trang)
- Form tạo CLB mới
- Form chỉnh sửa CLB
- Xóa CLB (soft delete nếu có)
- Upload logo, banner

**Chức năng Chủ nhiệm:**
- Xem thông tin CLB của mình
- Cập nhật thông tin CLB
- Upload logo, banner

**File liên quan:**
- `app/Http/Controllers/Admin/ClubController.php`
- `app/Http/Controllers/Student/ChairmanController.php`
- `resources/views/admin/clubs/index.blade.php`
- `resources/views/admin/clubs/create.blade.php`
- `resources/views/admin/clubs/edit.blade.php`

#### 3.3.2. Phê duyệt CLB

**Chức năng:**
- Đề xuất CLB mới (sinh viên)
- Danh sách đề xuất CLB (Admin)
- Phê duyệt/từ chối CLB
- Gửi thông báo kết quả

**File liên quan:**
- `app/Http/Controllers/StudentController.php` (proposeClub)
- `app/Http/Controllers/Admin/ClubController.php` (approve/reject)
- `app/Models/ClubProposal.php`

### 3.4. Xây dựng chức năng quản lý thành viên

#### 3.4.1. Đăng ký tham gia CLB

**Chức năng:**
- Form đăng ký tham gia CLB
- Lưu đơn đăng ký với trạng thái "pending"
- Gửi thông báo cho Chủ nhiệm
- Xem trạng thái đơn đăng ký

**File liên quan:**
- `app/Http/Controllers/StudentController.php` (registerClub)
- `database/migrations/2025_12_11_130000_create_club_registrations_table.php`

#### 3.4.2. Phê duyệt và quản lý thành viên

**Chức năng Chủ nhiệm:**
- Danh sách đơn đăng ký
- Phê duyệt/từ chối đơn đăng ký
- Danh sách thành viên CLB
- Tìm kiếm, lọc thành viên
- Đình chỉ/Kích hoạt thành viên
- Xóa thành viên

**File liên quan:**
- `app/Http/Controllers/Student/ChairmanController.php`
- `resources/views/student/chairman/manage-members.blade.php`
- `resources/views/student/chairman/manage-registrations.blade.php`

#### 3.4.3. Quản lý chức vụ trong CLB

**Chức năng:**
- Gán chức vụ cho thành viên
- Các chức vụ: Chủ nhiệm, Phó CN, Thư ký, Trưởng ban...
- Phân quyền theo chức vụ

**File liên quan:**
- `app/Http/Controllers/Student/ChairmanController.php` (updatePosition)
- `database/migrations/2025_12_11_120000_add_new_positions_to_club_members_table.php`

### 3.5. Xây dựng chức năng quản lý hoạt động

#### 3.5.1. Tạo và quản lý hoạt động

**Chức năng Chủ nhiệm:**
- Form tạo hoạt động mới
- Nhập thông tin: Tên, loại, mục tiêu, thời gian, địa điểm, số lượng, kinh phí, file đính kèm
- Danh sách hoạt động CLB
- Chỉnh sửa hoạt động
- Xóa hoạt động

**File liên quan:**
- `app/Http/Controllers/Student/ChairmanController.php` (createEvent, storeEvent)
- `resources/views/student/chairman/create-event.blade.php`

#### 3.5.2. Đề xuất hoạt động

**Chức năng Thành viên:**
- Form đề xuất hoạt động
- Gửi đề xuất với trạng thái "pending"
- Xem danh sách đề xuất của mình
- Xem lý do từ chối (nếu có)

**Chức năng Chủ nhiệm:**
- Danh sách đề xuất hoạt động
- Duyệt/từ chối đề xuất
- Chỉnh sửa đề xuất khi duyệt

**File liên quan:**
- `app/Http/Controllers/StudentController.php` (proposeEvent, storeProposedEvent)
- `app/Http/Controllers/Student/ChairmanController.php` (approveProposal)
- `resources/views/student/propose-event.blade.php`

#### 3.5.3. Đăng ký tham gia hoạt động

**Chức năng Thành viên:**
- Xem danh sách hoạt động
- Đăng ký tham gia hoạt động
- Hủy đăng ký (nếu chưa được duyệt)
- Xem trạng thái đăng ký

**Chức năng Chủ nhiệm:**
- Danh sách đăng ký tham gia
- Phê duyệt/từ chối đăng ký
- Quản lý danh sách tham gia

**File liên quan:**
- `app/Http/Controllers/StudentController.php` (registerEvent, cancelEventRegistration)
- `app/Http/Controllers/Student/ChairmanController.php` (manageRegistrations)
- `database/migrations/2025_12_11_130001_create_event_registrations_table.php`

#### 3.5.4. Theo dõi và thống kê hoạt động

**Chức năng:**
- Quản lý điểm hoạt động
- Lịch sử điểm hoạt động
- Thống kê tham gia hoạt động
- Xuất báo cáo

**File liên quan:**
- `app/Http/Controllers/Student/ChairmanController.php` (activityPoints, activityPointsHistory)
- `resources/views/student/chairman/activity-points.blade.php`

### 3.6. Xây dựng chức năng quản lý nội quy - Vi phạm

#### 3.6.1. Quản lý nội quy

**Chức năng Admin:**
- Danh sách nội quy
- Form tạo/chỉnh sửa nội quy
- Quản lý hiệu lực nội quy (kích hoạt/ngừng áp dụng)
- Lọc theo phạm vi (hệ thống/CLB cụ thể), mức độ, trạng thái

**File liên quan:**
- `app/Http/Controllers/Admin/RegulationController.php`
- `app/Models/Regulation.php`
- `database/migrations/2025_12_16_173842_create_regulations_table.php`

#### 3.6.2. Ghi nhận vi phạm

**Chức năng Chủ nhiệm:**
- Form ghi nhận vi phạm
- Chọn thành viên, nội quy vi phạm
- Mô tả vi phạm, mức độ
- Lưu vi phạm với trạng thái "chưa xử lý"

**File liên quan:**
- `app/Http/Controllers/Student/ChairmanController.php` (createViolation, storeViolation)
- `resources/views/student/chairman/violations/create.blade.php`

#### 3.6.3. Xử lý kỷ luật

**Chức năng Admin:**
- Danh sách vi phạm cần xử lý
- Form xử lý kỷ luật
- Chọn hình thức kỷ luật (Cảnh cáo, Khiển trách, Đình chỉ, Buộc rời CLB...)
- Nhập lý do, thời hạn kỷ luật
- Cập nhật trạng thái vi phạm

**File liên quan:**
- `app/Http/Controllers/Admin/ViolationController.php` (handle, processDiscipline)
- `resources/views/admin/violations/handle.blade.php`

#### 3.6.4. Lịch sử kỷ luật

**Chức năng:**
- Lịch sử theo thành viên (xem tất cả vi phạm của 1 thành viên)
- Lịch sử theo thời gian (thống kê vi phạm trong khoảng thời gian)
- Xuất báo cáo PDF/Excel

**File liên quan:**
- `app/Http/Controllers/Admin/ViolationController.php` (history, exportHistory)
- `app/Http/Controllers/Student/ChairmanController.php` (disciplineHistoryByMember, disciplineHistoryByTime)
- `resources/views/admin/violations/history.blade.php`
- `resources/views/student/chairman/discipline-history/by-member.blade.php`
- `resources/views/student/chairman/discipline-history/by-time.blade.php`

### 3.7. Xây dựng chức năng thống kê và báo cáo

#### 3.7.1. Thống kê hoạt động

**Chức năng Admin:**
- Dashboard tổng quan (số CLB, thành viên, hoạt động...)
- Thống kê theo CLB
- Thống kê theo thời gian
- Biểu đồ thống kê

**File liên quan:**
- `app/Http/Controllers/AdminController.php` (dashboard)
- `app/Http/Controllers/Admin/ActivityController.php` (statisticsByClub, statisticsByTime)

#### 3.7.2. Xuất báo cáo

**Chức năng:**
- Xuất danh sách hoạt động (Excel/PDF)
- Xuất danh sách vi phạm (Excel/PDF)
- Xuất lịch sử kỷ luật (Excel/PDF)
- Báo cáo thống kê tổng hợp

**File liên quan:**
- `app/Http/Controllers/Admin/ActivityController.php` (exportReport)
- `app/Http/Controllers/Admin/ViolationController.php` (export, exportHistory)
- `resources/views/admin/activities/export-pdf.blade.php`
- `resources/views/admin/violations/export-pdf.blade.php`

### 3.8. Giao diện và trải nghiệm người dùng

#### 3.8.1. Thiết kế giao diện tổng thể

- **Layout:** Sidebar navigation, Header, Content area
- **Màu sắc:** Theme nhất quán, phân biệt vai trò
- **Typography:** Font chữ dễ đọc, kích thước phù hợp
- **Icons:** Bootstrap Icons cho các chức năng
- **Responsive:** Mobile-first approach

#### 3.8.2. Đánh giá mức độ thân thiện

- **Dễ sử dụng:** Giao diện trực quan, dễ hiểu
- **Tốc độ:** Tải trang nhanh, phản hồi kịp thời
- **Tính nhất quán:** Layout và style đồng nhất
- **Hỗ trợ:** Thông báo rõ ràng, hướng dẫn sử dụng

---

## CHƯƠNG 4: KIỂM THỬ, ĐÁNH GIÁ VÀ KẾT QUẢ ĐẠT ĐƯỢC

### 4.1. Kiểm thử hệ thống

#### 4.1.1. Kiểm thử chức năng

**A. Kiểm thử đăng nhập/đăng xuất**
- Đăng nhập thành công với thông tin đúng
- Đăng nhập thất bại với thông tin sai
- Đăng xuất thành công
- Session được quản lý đúng

**B. Kiểm thử quản lý CLB**
- Tạo CLB mới thành công
- Chỉnh sửa thông tin CLB
- Phê duyệt/từ chối CLB
- Upload logo, banner

**C. Kiểm thử quản lý thành viên**
- Đăng ký tham gia CLB
- Phê duyệt/từ chối đơn đăng ký
- Gán chức vụ cho thành viên
- Đình chỉ/Kích hoạt thành viên

**D. Kiểm thử quản lý hoạt động**
- Tạo hoạt động mới
- Đề xuất hoạt động
- Duyệt đề xuất
- Đăng ký tham gia hoạt động
- Duyệt đăng ký tham gia

**E. Kiểm thử quản lý nội quy - Vi phạm**
- Tạo/chỉnh sửa nội quy
- Ghi nhận vi phạm
- Xử lý kỷ luật
- Xem lịch sử kỷ luật

**F. Kiểm thử thống kê và báo cáo**
- Xem thống kê
- Xuất báo cáo Excel/PDF
- Lọc và tìm kiếm

#### 4.1.2. Kiểm thử phân quyền

**A. Kiểm thử quyền Admin**
- Chỉ Admin mới truy cập được các route admin
- Admin có thể quản lý tất cả CLB
- Admin có thể xử lý kỷ luật

**B. Kiểm thử quyền Chủ nhiệm**
- Chỉ Chủ nhiệm CLB mới quản lý được CLB của mình
- Không thể truy cập CLB khác
- Có thể duyệt đơn đăng ký, đề xuất

**C. Kiểm thử quyền Thành viên**
- Chỉ xem được thông tin công khai
- Có thể đăng ký tham gia CLB, hoạt động
- Không thể quản lý CLB

#### 4.1.3. Kiểm thử giao diện

- Giao diện hiển thị đúng trên các trình duyệt (Chrome, Firefox, Edge)
- Responsive trên mobile, tablet, desktop
- Form validation hoạt động đúng
- Thông báo lỗi/thành công hiển thị rõ ràng

#### 4.1.4. Kiểm thử bảo mật

- Mật khẩu được mã hóa (bcrypt)
- CSRF protection hoạt động
- SQL injection được ngăn chặn (Eloquent ORM)
- XSS được ngăn chặn (Blade escaping)

### 4.2. Kết quả đạt được

#### 4.2.1. Các chức năng đã hoàn thành

**A. Quản lý người dùng**
- ✅ Đăng nhập, đăng xuất
- ✅ Đăng ký tài khoản
- ✅ Phân quyền theo vai trò
- ✅ Quản lý hồ sơ cá nhân

**B. Quản lý CLB**
- ✅ Tạo và quản lý CLB
- ✅ Phê duyệt CLB
- ✅ Quản lý thông tin CLB (logo, banner, mô tả...)

**C. Quản lý thành viên**
- ✅ Đăng ký tham gia CLB
- ✅ Phê duyệt thành viên
- ✅ Quản lý chức vụ
- ✅ Đình chỉ/Kích hoạt thành viên

**D. Quản lý hoạt động**
- ✅ Tạo hoạt động
- ✅ Đề xuất hoạt động
- ✅ Duyệt hoạt động
- ✅ Đăng ký tham gia hoạt động
- ✅ Quản lý điểm hoạt động

**E. Quản lý nội quy - Vi phạm**
- ✅ Quản lý nội quy
- ✅ Ghi nhận vi phạm
- ✅ Xử lý kỷ luật
- ✅ Lịch sử kỷ luật

**F. Thống kê và báo cáo**
- ✅ Thống kê hoạt động
- ✅ Thống kê vi phạm
- ✅ Xuất báo cáo Excel/PDF

**G. Thông báo**
- ✅ Gửi thông báo
- ✅ Lịch sử thông báo

#### 4.2.2. Mức độ đáp ứng yêu cầu đề tài

- **Yêu cầu chức năng:** Đạt 95%
- **Yêu cầu phi chức năng:** Đạt 90%
- **Giao diện:** Đạt 90%
- **Bảo mật:** Đạt 85%

### 4.3. Đánh giá hệ thống

#### 4.3.1. Ưu điểm

- **Tính toàn diện:** Hệ thống quản lý đầy đủ các chức năng cần thiết
- **Phân quyền rõ ràng:** Phân quyền theo vai trò, dễ quản lý
- **Giao diện thân thiện:** Dễ sử dụng, responsive
- **Tính mở rộng:** Code có cấu trúc rõ ràng, dễ bảo trì và mở rộng
- **Bảo mật:** Có các biện pháp bảo mật cơ bản
- **Thống kê và báo cáo:** Cung cấp đầy đủ công cụ thống kê và xuất báo cáo

#### 4.3.2. Hạn chế

- **Chưa có API:** Chưa xây dựng API cho mobile app
- **Chưa có real-time:** Thông báo chưa real-time (cần refresh)
- **Chưa có chat:** Chưa có tính năng chat giữa thành viên
- **Chưa có đa ngôn ngữ:** Chỉ hỗ trợ tiếng Việt
- **Chưa có backup tự động:** Cần cấu hình backup thủ công

### 4.4. So sánh với phương pháp quản lý truyền thống

| Tiêu chí | Phương pháp truyền thống | Hệ thống web |
|----------|-------------------------|--------------|
| **Lưu trữ dữ liệu** | File Word/Excel, sổ sách | Database tập trung |
| **Tìm kiếm** | Mất nhiều thời gian | Tìm kiếm nhanh chóng |
| **Phê duyệt** | Qua email/giấy tờ | Tự động, có thông báo |
| **Thống kê** | Thủ công, dễ sai sót | Tự động, chính xác |
| **Truy cập** | Tại văn phòng | Mọi lúc, mọi nơi |
| **Minh bạch** | Khó theo dõi | Công khai, minh bạch |
| **Quản lý kỷ luật** | Khó theo dõi | Có lịch sử đầy đủ |

---

## CHƯƠNG 5: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN

### 5.1. Kết luận

Hệ thống quản lý câu lạc bộ sinh viên đã được xây dựng thành công với đầy đủ các chức năng cơ bản:

- Quản lý CLB và thành viên hiệu quả
- Tự động hóa quy trình phê duyệt
- Quản lý hoạt động và điểm hoạt động
- Quản lý nội quy, vi phạm và kỷ luật
- Thống kê và báo cáo đầy đủ

Hệ thống đáp ứng được nhu cầu thực tế của việc quản lý CLB tại Trường Đại học Trà Vinh, góp phần nâng cao hiệu quả quản lý và tính minh bạch trong hoạt động CLB.

### 5.2. Hướng phát triển

#### 5.2.1. Ngắn hạn (3-6 tháng)

- **Tối ưu hiệu năng:** Cache, tối ưu query
- **Cải thiện UI/UX:** Thiết kế lại giao diện hiện đại hơn
- **Thêm tính năng:** Chat giữa thành viên, bình luận hoạt động
- **Real-time notification:** Sử dụng WebSocket hoặc Laravel Echo

#### 5.2.2. Trung hạn (6-12 tháng)

- **Mobile App:** Xây dựng ứng dụng mobile (React Native/Flutter)
- **API:** Xây dựng RESTful API cho mobile app
- **Đa ngôn ngữ:** Hỗ trợ tiếng Anh
- **Tích hợp thanh toán:** Thanh toán phí hoạt động online

#### 5.2.3. Dài hạn (1-2 năm)

- **AI/ML:** Gợi ý hoạt động phù hợp với thành viên
- **Blockchain:** Lưu trữ chứng chỉ, điểm hoạt động trên blockchain
- **Tích hợp hệ thống:** Tích hợp với hệ thống quản lý sinh viên của trường
- **Cloud deployment:** Triển khai lên cloud (AWS, Azure)

### 5.3. Khó khăn và hướng khắc phục trong quá trình thực hiện

#### 5.3.1. Khó khăn gặp phải

**A. Kỹ thuật**
- Chưa quen với Laravel framework
- Xử lý file upload và storage
- Xuất PDF với định dạng phức tạp
- Tối ưu query với nhiều quan hệ

**B. Nghiệp vụ**
- Hiểu rõ quy trình quản lý CLB thực tế
- Thiết kế database phù hợp với nghiệp vụ
- Phân quyền phức tạp (nhiều vai trò)

**C. Thời gian**
- Thời gian phát triển hạn chế
- Cần cân bằng giữa học tập và làm đồ án

#### 5.3.2. Hướng khắc phục

- **Tài liệu:** Đọc tài liệu Laravel chính thức, tham khảo các project mẫu
- **Cộng đồng:** Tham gia các forum, group Laravel để học hỏi
- **Thực hành:** Làm nhiều bài tập nhỏ trước khi làm project lớn
- **Phân tích:** Phân tích kỹ yêu cầu nghiệp vụ trước khi code
- **Quản lý thời gian:** Lập kế hoạch chi tiết, ưu tiên các chức năng quan trọng

---

## TÀI LIỆU THAM KHẢO

1. Laravel Documentation. (2024). *Laravel 12.x Documentation*. https://laravel.com/docs/12.x

2. PHP Documentation. (2024). *PHP Manual*. https://www.php.net/manual/en/

3. MySQL Documentation. (2024). *MySQL 8.0 Reference Manual*. https://dev.mysql.com/doc/

4. Bootstrap Documentation. (2024). *Bootstrap 5 Documentation*. https://getbootstrap.com/docs/5.3/

5. Barryvdh\DomPDF. (2024). *Laravel PDF Documentation*. https://github.com/barryvdh/laravel-dompdf

6. Trường Đại học Trà Vinh. (2024). *Quy chế hoạt động câu lạc bộ sinh viên*.

7. [Tài liệu khác nếu có]

---

## PHỤ LỤC

### PHỤ LỤC A: Hình ảnh giao diện hệ thống

#### A.1. Giao diện đăng nhập
*(Chèn ảnh màn hình đăng nhập)*

#### A.2. Giao diện Admin
- Dashboard
- Quản lý CLB
- Quản lý hoạt động
- Quản lý nội quy - Vi phạm

#### A.3. Giao diện Chủ nhiệm
- Dashboard
- Quản lý thành viên
- Quản lý hoạt động
- Quản lý vi phạm

#### A.4. Giao diện Thành viên
- Trang chủ
- CLB của tôi
- Đề xuất hoạt động
- Điểm hoạt động

### PHỤ LỤC B: Mã nguồn minh họa

#### B.1. Controller mẫu
```php
// Ví dụ: AdminController.php
```

#### B.2. Model mẫu
```php
// Ví dụ: Club.php
```

#### B.3. Migration mẫu
```php
// Ví dụ: create_clubs_table.php
```

#### B.4. View mẫu
```blade
{{-- Ví dụ: index.blade.php --}}
```

### PHỤ LỤC C: Biểu mẫu sử dụng

#### C.1. Biểu mẫu đăng ký CLB
*(Chèn ảnh form đăng ký)*

#### C.2. Biểu mẫu tạo hoạt động
*(Chèn ảnh form tạo hoạt động)*

#### C.3. Biểu mẫu ghi nhận vi phạm
*(Chèn ảnh form vi phạm)*

### PHỤ LỤC D: Sơ đồ Use Case chi tiết

*(Tham khảo file `DIAGRAMS_USE_CASE.md`)*

### PHỤ LỤC E: Sơ đồ ERD

*(Chèn sơ đồ ERD chi tiết)*

### PHỤ LỤC F: Bảng dữ liệu mẫu

*(Chèn ảnh các bảng dữ liệu từ phpMyAdmin)*

---

**KẾT THÚC BÁO CÁO**

