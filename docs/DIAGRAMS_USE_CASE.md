# SƠ ĐỒ USE CASE - HỆ THỐNG QUẢN LÝ CLB

## 1. SƠ ĐỒ USE CASE TỔNG QUÁT

```mermaid
graph TB
    Admin[Admin]
    SV_QuanLy[Sinh viên - Quản lý CLB]
    SV_ThanhVien[Sinh viên - Thành viên]
    
    subgraph HeThong["HỆ THỐNG QUẢN LÝ CLB"]
        UC1[Quản lý CLB]
        UC2[Quản lý tài khoản]
        UC3[Quản lý hoạt động]
        UC4[Quản lý thành viên]
        UC5[Quản lý nội quy - Vi phạm]
        UC6[Thống kê - Báo cáo]
        UC7[Đăng ký CLB]
        UC8[Tham gia hoạt động]
        UC9[Đề xuất hoạt động]
        UC10[Xem thông tin]
    end
    
    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC5
    Admin --> UC6
    
    SV_QuanLy --> UC3
    SV_QuanLy --> UC4
    SV_QuanLy --> UC5
    SV_QuanLy --> UC6
    SV_QuanLy --> UC10
    
    SV_ThanhVien --> UC7
    SV_ThanhVien --> UC8
    SV_ThanhVien --> UC9
    SV_ThanhVien --> UC10
    
    style Admin fill:#ff6b6b
    style SV_QuanLy fill:#4ecdc4
    style SV_ThanhVien fill:#95e1d3
```

---

## 2. SƠ ĐỒ USE CASE ADMIN

```mermaid
graph TB
    Admin[Admin]
    
    subgraph QuanTri["QUẢN TRỊ HỆ THỐNG"]
        UC1[Quản lý tài khoản]
        UC2[Phân quyền & vai trò]
        UC3[Quản lý CLB]
        UC4[Quản lý thành viên CLB]
        UC5[Cấu hình hệ thống]
    end
    
    subgraph HoatDong["QUẢN LÝ HOẠT ĐỘNG"]
        UC6[Danh sách hoạt động]
        UC7[Hoạt động vi phạm]
        UC8[Thống kê hoạt động]
        UC9[Xuất báo cáo]
    end
    
    subgraph NoiQuy["NỘI QUY - VI PHẠM"]
        UC10[Danh sách nội quy]
        UC11[Cập nhật nội quy]
        UC12[Danh sách vi phạm]
        UC13[Xử lý kỷ luật]
        UC14[Lịch sử kỷ luật]
    end
    
    subgraph ThongKe["THỐNG KÊ - BÁO CÁO"]
        UC15[Thống kê hoạt động]
        UC16[Báo cáo tài chính]
        UC17[Xuất dữ liệu]
    end
    
    subgraph ThongBao["THÔNG BÁO HỆ THỐNG"]
        UC18[Gửi thông báo]
        UC19[Lịch sử thông báo]
    end
    
    subgraph HeThong["HỆ THỐNG"]
        UC20[Sao lưu dữ liệu]
        UC21[Nhật ký Admin]
        UC22[Cấu hình Website]
    end
    
    Admin --> QuanTri
    Admin --> HoatDong
    Admin --> NoiQuy
    Admin --> ThongKe
    Admin --> ThongBao
    Admin --> HeThong
    
    style Admin fill:#ff6b6b
    style QuanTri fill:#ffe66d
    style HoatDong fill:#a8e6cf
    style NoiQuy fill:#ffaaa5
    style ThongKe fill:#95e1d3
    style ThongBao fill:#ffd3b6
    style HeThong fill:#c7ceea
```

---

## 3. SƠ ĐỒ USE CASE SINH VIÊN - QUẢN LÝ CLB

```mermaid
graph TB
    SV_QL[Sinh viên - Quản lý CLB<br/>Chủ nhiệm/Phó CN/Thư ký/Trưởng ban]
    
    subgraph QuanLyThanhVien["QUẢN LÝ THÀNH VIÊN"]
        UC1[Quản lý thành viên CLB]
        UC2[Quản lý đơn đăng ký vào CLB]
        UC3[Phân quyền và gán chức vụ]
    end
    
    subgraph HoatDongCLB["HOẠT ĐỘNG CLB"]
        UC4[Xem danh sách hoạt động]
        UC5[Tạo hoạt động mới]
        UC6[Duyệt đăng ký tham gia hoạt động]
        UC7[Duyệt đề xuất hoạt động từ thành viên]
        UC8[Quản lý điểm hoạt động]
    end
    
    subgraph ThongKeBaoCao["THỐNG KÊ & BÁO CÁO"]
        UC9[Thống kê tham gia]
        UC10[Xuất báo cáo]
    end
    
    subgraph NoiQuyViPham["NỘI QUY - VI PHẠM"]
        UC11[Xem danh sách nội quy]
        UC12[Ghi nhận vi phạm]
        UC13[Xem danh sách vi phạm]
        UC14[Xem lịch sử kỷ luật theo thành viên]
        UC15[Xem lịch sử kỷ luật theo thời gian]
    end
    
    subgraph ThongTinCLB["THÔNG TIN CLB"]
        UC16[Quản lý thông tin CLB]
    end
    
    SV_QL --> QuanLyThanhVien
    SV_QL --> HoatDongCLB
    SV_QL --> ThongKeBaoCao
    SV_QL --> NoiQuyViPham
    SV_QL --> ThongTinCLB
    
    style SV_QL fill:#4ecdc4
    style QuanLyThanhVien fill:#ffe66d
    style HoatDongCLB fill:#a8e6cf
    style ThongKeBaoCao fill:#95e1d3
    style NoiQuyViPham fill:#ffaaa5
    style ThongTinCLB fill:#ffd3b6
```

### 3.1. Chi tiết Use Case - Quản lý thành viên

```mermaid
graph LR
    CN[Chủ nhiệm/Phó CN] --> UC1[Xem danh sách thành viên]
    CN --> UC2[Thêm thành viên mới]
    CN --> UC3[Cập nhật thông tin thành viên]
    CN --> UC4[Phê duyệt thành viên]
    CN --> UC5[Từ chối thành viên]
    CN --> UC6[Đình chỉ thành viên]
    CN --> UC7[Kích hoạt lại thành viên]
    CN --> UC8[Xóa thành viên]
    CN --> UC9[Phê duyệt đơn đăng ký CLB]
    CN --> UC10[Từ chối đơn đăng ký CLB]
    CN --> UC11[Gán chức vụ cho thành viên]
    
    style CN fill:#4ecdc4
```

### 3.2. Chi tiết Use Case - Hoạt động CLB

```mermaid
graph TB
    CN[Chủ nhiệm/Phó CN] --> UC1[Xem danh sách hoạt động]
    CN --> UC2[Tạo hoạt động mới<br/>- Tên, loại, mục tiêu<br/>- Thời gian, địa điểm<br/>- Số lượng, kinh phí<br/>- File đính kèm]
    CN --> UC3[Duyệt đăng ký tham gia<br/>- Phê duyệt<br/>- Từ chối kèm lý do]
    CN --> UC4[Duyệt đề xuất hoạt động<br/>- Duyệt và chỉnh sửa<br/>- Từ chối kèm lý do]
    CN --> UC5[Quản lý điểm hoạt động<br/>- Tính điểm<br/>- Xem lịch sử điểm]
    
    style CN fill:#4ecdc4
```

### 3.3. Chi tiết Use Case - Nội quy & Vi phạm

```mermaid
graph TB
    CN[Chủ nhiệm/Phó CN]
    
    subgraph NoiQuy["NỘI QUY"]
        UC1[Xem danh sách nội quy<br/>- Hệ thống<br/>- CLB]
    end
    
    subgraph ViPham["VI PHẠM & KỶ LUẬT"]
        UC2[Ghi nhận vi phạm<br/>- Chọn thành viên<br/>- Chọn nội quy<br/>- Mô tả, mức độ]
        UC3[Xem danh sách vi phạm]
    end
    
    subgraph LichSu["LỊCH SỬ KỶ LUẬT"]
        UC4[Theo thành viên<br/>- Thống kê<br/>- Chi tiết vi phạm]
        UC5[Theo thời gian<br/>- Thống kê theo khoảng thời gian<br/>- Top nội quy vi phạm]
    end
    
    CN --> NoiQuy
    CN --> ViPham
    CN --> LichSu
    
    style CN fill:#4ecdc4
```

---

## 4. SƠ ĐỒ USE CASE SINH VIÊN - THÀNH VIÊN

```mermaid
graph TB
    SV[Sinh viên - Thành viên]
    
    subgraph TrangChu["TRANG CHỦ"]
        UC1[Xem trang chủ sinh viên]
    end
    
    subgraph TimKiem["TÌM KIẾM & XEM"]
        UC2[Tìm kiếm CLB]
        UC3[Xem danh sách CLB]
        UC4[Xem thông tin CLB]
    end
    
    subgraph DangKy["ĐĂNG KÝ"]
        UC5[Đăng ký tham gia CLB]
    end
    
    subgraph CLBCuaToi["CLB CỦA TÔI"]
        UC6[Xem thông tin CLB đang tham gia]
        UC7[Xem danh sách thành viên]
        UC8[Xem sự kiện sắp tới]
        UC9[Đăng ký tham gia hoạt động]
        UC10[Hủy đăng ký hoạt động]
        UC11[Xem chi tiết hoạt động]
    end
    
    subgraph DeXuat["ĐỀ XUẤT HOẠT ĐỘNG"]
        UC12[Đề xuất hoạt động mới]
        UC13[Xem danh sách đề xuất của mình]
        UC14[Xem chi tiết đề xuất]
        UC15[Xem lý do từ chối]
    end
    
    subgraph Diem["ĐIỂM HOẠT ĐỘNG"]
        UC16[Xem điểm hoạt động]
        UC17[Xem lịch sử điểm]
    end
    
    subgraph ThongBao["THÔNG BÁO"]
        UC18[Xem thông báo]
        UC19[Đánh dấu đã đọc]
    end
    
    subgraph CaNhan["HỒ SƠ CÁ NHÂN"]
        UC20[Xem hồ sơ cá nhân]
        UC21[Cập nhật thông tin]
        UC22[Đổi mật khẩu]
        UC23[Xem lịch sử vi phạm]
    end
    
    SV --> TrangChu
    SV --> TimKiem
    SV --> DangKy
    SV --> CLBCuaToi
    SV --> DeXuat
    SV --> Diem
    SV --> ThongBao
    SV --> CaNhan
    
    style SV fill:#95e1d3
    style TrangChu fill:#ffe66d
    style TimKiem fill:#a8e6cf
    style DangKy fill:#ffaaa5
    style CLBCuaToi fill:#95e1d3
    style DeXuat fill:#ffd3b6
    style Diem fill:#c7ceea
    style ThongBao fill:#f38181
    style CaNhan fill:#aa96da
```

### 4.1. Chi tiết Use Case - Đề xuất hoạt động

```mermaid
sequenceDiagram
    participant SV as Sinh viên
    participant HT as Hệ thống
    participant CN as Chủ nhiệm
    
    SV->>HT: Đề xuất hoạt động mới
    SV->>HT: Điền thông tin<br/>(Tên, loại, mục tiêu,<br/>thời gian, địa điểm, ...)
    SV->>HT: Gửi đề xuất
    HT->>HT: Lưu với trạng thái<br/>"Chờ duyệt"
    HT->>CN: Gửi thông báo<br/>có đề xuất mới
    CN->>HT: Xem đề xuất
    CN->>HT: Duyệt/Từ chối
    alt Đã duyệt
        HT->>HT: Tạo hoạt động chính thức
        HT->>SV: Thông báo đề xuất đã được duyệt
    else Bị từ chối
        HT->>SV: Thông báo từ chối kèm lý do
    end
```

### 4.2. Chi tiết Use Case - Đăng ký hoạt động

```mermaid
sequenceDiagram
    participant SV as Sinh viên
    participant HT as Hệ thống
    participant CN as Chủ nhiệm
    
    SV->>HT: Xem danh sách hoạt động
    SV->>HT: Chọn hoạt động
    SV->>HT: Nhấn "Đăng ký tham gia"
    HT->>HT: Lưu đăng ký với<br/>trạng thái "Chờ duyệt"
    HT->>CN: Gửi thông báo<br/>có đơn đăng ký mới
    CN->>HT: Xem danh sách đăng ký
    CN->>HT: Phê duyệt/Từ chối
    alt Đã phê duyệt
        HT->>SV: Thông báo đã được duyệt
        HT->>SV: Hiển thị trạng thái<br/>"Đã đăng ký"
    else Bị từ chối
        HT->>SV: Thông báo từ chối kèm lý do
        HT->>SV: Hiển thị trạng thái<br/>"Bị từ chối"
    end
```

---

## 5. SƠ ĐỒ USE CASE CHI TIẾT - QUẢN LÝ HOẠT ĐỘNG CLB

```mermaid
graph TB
    CN[Chủ nhiệm/Phó CN] --> A[Tạo hoạt động mới]
    A --> A1[Nhập thông tin cơ bản]
    A1 --> A2[Nhập thông tin chi tiết]
    A2 --> A3[Upload file đính kèm]
    A3 --> A4[Lưu và tạo hoạt động]
    
    CN --> B[Duyệt đăng ký tham gia]
    B --> B1[Xem danh sách đơn đăng ký]
    B1 --> B2[Phê duyệt đơn]
    B1 --> B3[Từ chối đơn]
    B2 --> B4[Gửi thông báo cho sinh viên]
    B3 --> B5[Gửi thông báo từ chối]
    
    CN --> C[Duyệt đề xuất hoạt động]
    C --> C1[Xem danh sách đề xuất]
    C1 --> C2[Duyệt và chỉnh sửa]
    C1 --> C3[Từ chối đề xuất]
    C2 --> C4[Tạo hoạt động chính thức]
    C3 --> C5[Gửi thông báo từ chối]
    
    CN --> D[Quản lý điểm hoạt động]
    D --> D1[Xem danh sách tham gia]
    D1 --> D2[Gán điểm cho thành viên]
    D2 --> D3[Lưu điểm]
    D3 --> D4[Xem lịch sử điểm]
    
    style CN fill:#4ecdc4
```

---

## 6. SƠ ĐỒ USE CASE CHI TIẾT - NỘI QUY & VI PHẠM

```mermaid
graph TB
    CN[Chủ nhiệm/Phó CN]
    
    CN --> A[Xem danh sách nội quy]
    A --> A1[Nội quy hệ thống]
    A --> A2[Nội quy CLB]
    
    CN --> B[Ghi nhận vi phạm]
    B --> B1[Chọn thành viên vi phạm]
    B1 --> B2[Chọn nội quy vi phạm]
    B2 --> B3[Nhập mô tả vi phạm]
    B3 --> B4[Chọn mức độ vi phạm]
    B4 --> B5[Lưu vi phạm]
    B5 --> B6[Admin xử lý kỷ luật]
    
    CN --> C[Xem danh sách vi phạm]
    C --> C1[Lọc theo mức độ]
    C --> C2[Lọc theo trạng thái]
    C --> C3[Xem chi tiết vi phạm]
    
    CN --> D[Lịch sử kỷ luật]
    D --> D1[Theo thành viên<br/>- Thống kê<br/>- Chi tiết từng vi phạm]
    D --> D2[Theo thời gian<br/>- Thống kê tổng hợp<br/>- Top nội quy vi phạm]
    
    style CN fill:#4ecdc4
    style B6 fill:#ff6b6b
```

---

## GHI CHÚ

- Các sơ đồ được vẽ bằng Mermaid syntax, có thể xem trực tiếp trên GitHub, GitLab, hoặc các trình xem markdown hỗ trợ Mermaid
- Để xem sơ đồ, cần sử dụng trình xem markdown có hỗ trợ Mermaid (như VS Code với extension Mermaid Preview, hoặc GitHub)
- Màu sắc được phân biệt để dễ nhận biết các nhóm chức năng
- Các use case có thể mở rộng thêm tùy theo yêu cầu của hệ thống


