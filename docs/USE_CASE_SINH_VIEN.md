# USE CASE - SINH VIÊN

## 1. USE CASE CHO SINH VIÊN – QUẢN LÝ CLB

### 1.1. Đối tượng người dùng
- **Chủ nhiệm (Chairman)**
- **Phó Chủ nhiệm (Vice Chairman)**
- **Thư ký (Secretary)**
- **Trưởng ban chuyên môn (Professional Leader)**
- **Trưởng ban truyền thông (Communication Leader)**
- **Trưởng ban hoạt động (Activity Leader)**
- **Trưởng ban tài chính (Finance Leader)**

### 1.2. UC1: Quản lý thành viên CLB

**Mô tả:** Quản lý danh sách thành viên, thêm/xóa/sửa thông tin thành viên, phê duyệt/hủy thành viên.

**Người thực hiện:** Chủ nhiệm, Phó Chủ nhiệm

**Luồng chính:**
1. Truy cập trang "Quản lý thành viên"
2. Xem danh sách thành viên CLB
3. Tìm kiếm/lọc thành viên
4. Thực hiện các thao tác:
   - Thêm thành viên mới
   - Cập nhật thông tin thành viên
   - Phê duyệt thành viên
   - Từ chối thành viên
   - Đình chỉ thành viên
   - Kích hoạt lại thành viên
   - Xóa thành viên

**Điều kiện tiên quyết:** Đăng nhập với quyền Chủ nhiệm/Phó Chủ nhiệm

**Điều kiện sau:** Danh sách thành viên được cập nhật

---

### 1.3. UC2: Quản lý đơn đăng ký vào CLB

**Mô tả:** Xử lý các đơn đăng ký tham gia CLB từ sinh viên.

**Người thực hiện:** Chủ nhiệm, Phó Chủ nhiệm

**Luồng chính:**
1. Truy cập trang "Đơn đăng ký"
2. Xem danh sách đơn đăng ký đang chờ duyệt
3. Xem chi tiết đơn đăng ký
4. Quyết định:
   - Phê duyệt đơn đăng ký
   - Từ chối đơn đăng ký (kèm lý do)

**Điều kiện tiên quyết:** Có đơn đăng ký mới từ sinh viên

**Điều kiện sau:** Sinh viên nhận được thông báo kết quả

---

### 1.4. UC3: Phân quyền và gán chức vụ

**Mô tả:** Gán chức vụ và phân quyền cho thành viên trong CLB.

**Người thực hiện:** Chủ nhiệm

**Luồng chính:**
1. Truy cập trang "Phân quyền"
2. Xem danh sách thành viên và chức vụ hiện tại
3. Chọn thành viên cần thay đổi chức vụ
4. Gán chức vụ mới:
   - Phó Chủ nhiệm
   - Thư ký
   - Trưởng ban chuyên môn
   - Trưởng ban truyền thông
   - Trưởng ban hoạt động
   - Trưởng ban tài chính
   - Thành viên
5. Lưu thay đổi

**Điều kiện tiên quyết:** Thành viên đã được phê duyệt

**Điều kiện sau:** Chức vụ của thành viên được cập nhật

---

### 1.5. UC4: Quản lý hoạt động CLB

#### 1.5.1. UC4.1: Xem danh sách hoạt động

**Mô tả:** Xem danh sách các hoạt động đã được phê duyệt của CLB.

**Người thực hiện:** Tất cả vai trò quản lý

**Luồng chính:**
1. Truy cập "HOẠT ĐỘNG CLB" → "Danh sách hoạt động"
2. Xem danh sách hoạt động
3. Xem chi tiết hoạt động
4. Lọc/tìm kiếm hoạt động

---

#### 1.5.2. UC4.2: Tạo hoạt động mới

**Mô tả:** Tạo hoạt động mới cho CLB.

**Người thực hiện:** Chủ nhiệm, Phó Chủ nhiệm

**Luồng chính:**
1. Truy cập "HOẠT ĐỘNG CLB" → "Tạo hoạt động mới"
2. Điền thông tin hoạt động:
   - Tên hoạt động
   - Loại hoạt động (Học thuật/Văn nghệ/Tình nguyện/Khác)
   - Mục tiêu
   - Nội dung chi tiết
   - Thời gian (bắt đầu, kết thúc)
   - Địa điểm
   - Số lượng dự kiến
   - Kinh phí dự kiến
   - File đính kèm (nếu có)
3. Gửi đề xuất
4. Hoạt động được tạo với trạng thái "Đã duyệt" (tự động duyệt cho Chủ nhiệm/Phó Chủ nhiệm)

**Điều kiện sau:** Hoạt động được tạo và hiển thị trong danh sách

---

#### 1.5.3. UC4.3: Duyệt đăng ký tham gia hoạt động

**Mô tả:** Duyệt các đơn đăng ký tham gia hoạt động từ thành viên.

**Người thực hiện:** Chủ nhiệm, Phó Chủ nhiệm

**Luồng chính:**
1. Truy cập "HOẠT ĐỘNG CLB" → "Danh sách đăng ký"
2. Xem danh sách đơn đăng ký đang chờ duyệt
3. Xem chi tiết đơn đăng ký
4. Quyết định:
   - Phê duyệt đơn đăng ký
   - Từ chối đơn đăng ký (kèm lý do)

**Điều kiện sau:** Sinh viên nhận được thông báo kết quả

---

#### 1.5.4. UC4.4: Duyệt đề xuất hoạt động từ thành viên

**Mô tả:** Xem và duyệt các đề xuất hoạt động từ thành viên CLB.

**Người thực hiện:** Chủ nhiệm, Phó Chủ nhiệm

**Luồng chính:**
1. Truy cập "HOẠT ĐỘNG CLB" → "Danh sách đề xuất hoạt động"
2. Xem danh sách đề xuất với trạng thái (Chờ duyệt/Đã duyệt/Bị từ chối)
3. Xem chi tiết đề xuất
4. Quyết định:
   - Duyệt đề xuất (có thể chỉnh sửa: thời gian, địa điểm, nội dung)
   - Từ chối đề xuất (kèm lý do)

**Điều kiện sau:** 
- Nếu duyệt: Hoạt động chính thức được tạo, người đề xuất nhận thông báo
- Nếu từ chối: Người đề xuất nhận thông báo kèm lý do

---

#### 1.5.5. UC4.5: Quản lý điểm hoạt động

**Mô tả:** Tính điểm và quản lý lịch sử điểm hoạt động cho thành viên.

**Người thực hiện:** Chủ nhiệm, Phó Chủ nhiệm

**Luồng chính:**
1. Truy cập "HOẠT ĐỘNG CLB" → "Điểm hoạt động" → "Tính điểm"
2. Xem danh sách thành viên đã tham gia hoạt động
3. Gán điểm cho từng thành viên dựa trên mức độ tham gia
4. Lưu điểm
5. Xem "Lịch sử điểm" để tra cứu điểm của thành viên theo thời gian

**Điều kiện sau:** Điểm hoạt động được cập nhật vào hồ sơ thành viên

---

### 1.6. UC5: Thống kê & Báo cáo

#### 1.6.1. UC5.1: Thống kê tham gia

**Mô tả:** Xem thống kê về mức độ tham gia hoạt động của thành viên.

**Người thực hiện:** Tất cả vai trò quản lý

**Luồng chính:**
1. Truy cập "THỐNG KÊ & BÁO CÁO" → "Thống kê tham gia"
2. Xem các chỉ số tổng quan:
   - Tổng số hoạt động
   - Tổng lượt tham gia
   - Tổng số sinh viên tham gia
3. Xem biểu đồ thống kê:
   - Biểu đồ hoạt động theo tháng
   - Biểu đồ số lượng sinh viên tham gia theo tháng
   - Top hoạt động có số lượt tham gia cao nhất
4. Lọc theo khoảng thời gian, học kỳ/năm học

---

#### 1.6.2. UC5.2: Xuất báo cáo

**Mô tả:** Xuất báo cáo dữ liệu hoạt động ra file Excel/PDF.

**Người thực hiện:** Tất cả vai trò quản lý

**Luồng chính:**
1. Truy cập "THỐNG KÊ & BÁO CÁO" → "Xuất báo cáo"
2. Chọn loại báo cáo:
   - Báo cáo danh sách hoạt động
   - Báo cáo danh sách tham gia
   - Báo cáo điểm hoạt động
3. Chọn khoảng thời gian
4. Chọn định dạng (Excel/PDF)
5. Xuất file

**Điều kiện sau:** File báo cáo được tải về

---

### 1.7. UC6: Quản lý nội quy và vi phạm

#### 1.7.1. UC6.1: Xem danh sách nội quy

**Mô tả:** Xem các nội quy hệ thống và nội quy CLB.

**Người thực hiện:** Tất cả vai trò quản lý

**Luồng chính:**
1. Truy cập "NỘI QUY - VI PHẠM" → "Nội quy" → "Danh sách nội quy"
2. Xem danh sách nội quy (hệ thống và CLB)
3. Xem chi tiết nội quy
4. Tìm kiếm/lọc theo mức độ

**Lưu ý:** Chỉ xem, không được chỉnh sửa

---

#### 1.7.2. UC6.2: Ghi nhận vi phạm

**Mô tả:** Ghi nhận vi phạm của thành viên trong CLB.

**Người thực hiện:** Chủ nhiệm, Phó Chủ nhiệm

**Luồng chính:**
1. Truy cập "NỘI QUY - VI PHẠM" → "Vi phạm & kỷ luật" → "Ghi nhận vi phạm"
2. Chọn thành viên vi phạm
3. Chọn nội quy bị vi phạm
4. Nhập thông tin:
   - Mô tả hành vi vi phạm
   - Mức độ vi phạm (Nhẹ/Trung bình/Nghiêm trọng)
   - Thời gian xảy ra vi phạm
5. Lưu vi phạm

**Điều kiện sau:** Vi phạm được ghi nhận với trạng thái "Chưa xử lý", Admin có thể xem và xử lý

---

#### 1.7.3. UC6.3: Xem danh sách vi phạm

**Mô tả:** Xem danh sách vi phạm của thành viên trong CLB.

**Người thực hiện:** Chủ nhiệm, Phó Chủ nhiệm

**Luồng chính:**
1. Truy cập "NỘI QUY - VI PHẠM" → "Vi phạm & kỷ luật" → "Danh sách vi phạm"
2. Xem danh sách vi phạm
3. Lọc theo mức độ, trạng thái
4. Xem chi tiết vi phạm

---

#### 1.7.4. UC6.4: Xem lịch sử kỷ luật theo thành viên

**Mô tả:** Xem toàn bộ lịch sử vi phạm và kỷ luật của một thành viên cụ thể.

**Người thực hiện:** Chủ nhiệm, Phó Chủ nhiệm

**Luồng chính:**
1. Truy cập "NỘI QUY - VI PHẠM" → "Lịch sử kỷ luật" → "Theo thành viên"
2. Chọn thành viên từ danh sách
3. Xem thông tin thành viên và thống kê:
   - Tổng số lần vi phạm
   - Số lần vi phạm theo mức độ
4. Xem danh sách vi phạm chi tiết
5. Lọc theo mức độ, trạng thái

---

#### 1.7.5. UC6.5: Xem lịch sử kỷ luật theo thời gian

**Mô tả:** Xem thống kê vi phạm trong một khoảng thời gian.

**Người thực hiện:** Chủ nhiệm, Phó Chủ nhiệm

**Luồng chính:**
1. Truy cập "NỘI QUY - VI PHẠM" → "Lịch sử kỷ luật" → "Theo thời gian"
2. Chọn khoảng thời gian
3. Xem thống kê:
   - Tổng số vi phạm
   - Số thành viên vi phạm
   - Vi phạm theo mức độ
   - Top nội quy bị vi phạm
4. Xem danh sách vi phạm chi tiết
5. Lọc theo mức độ, trạng thái

---

### 1.8. UC7: Quản lý thông tin CLB

**Mô tả:** Xem và cập nhật thông tin CLB.

**Người thực hiện:** Chủ nhiệm

**Luồng chính:**
1. Truy cập "Thông tin CLB"
2. Xem thông tin hiện tại:
   - Tên CLB
   - Mã CLB
   - Logo
   - Banner
   - Mô tả
   - Quy mô
   - Lĩnh vực hoạt động
3. Chỉnh sửa thông tin (nếu cần)
4. Cập nhật logo/banner
5. Lưu thay đổi

**Điều kiện sau:** Thông tin CLB được cập nhật

---

## 2. USE CASE CHO SINH VIÊN – THÀNH VIÊN

### 2.1. Đối tượng người dùng
- **Thành viên CLB (Member)** - Sinh viên đã được phê duyệt tham gia CLB

### 2.2. UC1: Xem trang chủ sinh viên

**Mô tả:** Xem trang chủ với các thông tin tổng quan và hoạt động gần đây.

**Luồng chính:**
1. Đăng nhập vào hệ thống
2. Truy cập trang chủ sinh viên
3. Xem các thông tin:
   - CLB đang tham gia
   - Hoạt động sắp tới
   - Thông báo mới
   - Hoạt động đang diễn ra

---

### 2.3. UC2: Tìm kiếm và xem danh sách CLB

**Mô tả:** Tìm kiếm và xem thông tin các CLB trong hệ thống.

**Luồng chính:**
1. Truy cập "Tất cả CLB"
2. Tìm kiếm CLB theo tên, mã, lĩnh vực
3. Lọc CLB theo tiêu chí
4. Xem danh sách CLB
5. Xem thông tin chi tiết CLB

---

### 2.4. UC3: Đăng ký tham gia CLB

**Mô tả:** Gửi đơn đăng ký tham gia một CLB.

**Luồng chính:**
1. Xem thông tin chi tiết CLB
2. Nhấn nút "Đăng ký tham gia"
3. Xác nhận đăng ký
4. Chờ phê duyệt từ Chủ nhiệm/Phó Chủ nhiệm
5. Nhận thông báo kết quả (phê duyệt/từ chối)

**Điều kiện tiên quyết:** Chưa là thành viên của CLB

**Điều kiện sau:** Đơn đăng ký được gửi, trạng thái "Chờ duyệt"

---

### 2.5. UC4: Xem thông tin CLB đang tham gia

**Mô tả:** Xem thông tin chi tiết của CLB mà mình đang là thành viên.

**Luồng chính:**
1. Truy cập "CLB của tôi"
2. Xem thông tin CLB:
   - Thông tin chung
   - Danh sách thành viên
   - Hoạt động của CLB
   - Đề xuất hoạt động (nếu có)

---

### 2.6. UC5: Đề xuất hoạt động

**Mô tả:** Gửi đề xuất hoạt động mới cho CLB.

**Luồng chính:**
1. Truy cập "CLB của tôi" → Tab "Đề xuất" hoặc nút "Đề xuất hoạt động"
2. Điền thông tin đề xuất:
   - Tên hoạt động
   - Loại hoạt động
   - Mục tiêu
   - Nội dung chi tiết
   - Thời gian dự kiến
   - Địa điểm dự kiến
   - Số lượng dự kiến
   - Kinh phí dự kiến
   - File đính kèm (nếu có)
3. Gửi đề xuất
4. Chờ phê duyệt từ Chủ nhiệm/Phó Chủ nhiệm
5. Xem trạng thái đề xuất (Chờ duyệt/Đã duyệt/Bị từ chối)
6. Nếu bị từ chối: Xem lý do từ chối

**Điều kiện tiên quyết:** Là thành viên của CLB

**Điều kiện sau:** Đề xuất được gửi, trạng thái "Chờ duyệt"

---

### 2.7. UC6: Xem danh sách đề xuất của mình

**Mô tả:** Xem danh sách các đề xuất hoạt động mà mình đã gửi.

**Luồng chính:**
1. Truy cập "CLB của tôi" → Tab "Danh sách đề xuất"
2. Xem danh sách đề xuất với trạng thái
3. Xem chi tiết từng đề xuất
4. Nếu đã duyệt: Xem liên kết đến hoạt động chính thức

---

### 2.8. UC7: Đăng ký tham gia hoạt động

**Mô tả:** Đăng ký tham gia một hoạt động của CLB.

**Luồng chính:**
1. Xem danh sách hoạt động sắp tới
2. Chọn hoạt động muốn tham gia
3. Nhấn nút "Tham gia" hoặc "Đăng ký"
4. Chờ phê duyệt từ Chủ nhiệm/Phó Chủ nhiệm
5. Nhận thông báo kết quả (phê duyệt/từ chối)

**Điều kiện tiên quyết:** Là thành viên của CLB tổ chức hoạt động

**Điều kiện sau:** Đơn đăng ký được gửi, trạng thái "Chờ duyệt"

---

### 2.9. UC8: Hủy đăng ký tham gia hoạt động

**Mô tả:** Hủy đăng ký tham gia một hoạt động đã đăng ký.

**Luồng chính:**
1. Xem danh sách hoạt động đã đăng ký
2. Chọn hoạt động muốn hủy
3. Nhấn nút "Hủy đăng ký"
4. Xác nhận hủy
5. Trạng thái cập nhật thành "Đã hủy"

**Điều kiện tiên quyết:** Đã đăng ký tham gia hoạt động và chưa được duyệt

---

### 2.10. UC9: Xem chi tiết hoạt động

**Mô tả:** Xem thông tin chi tiết về một hoạt động.

**Luồng chính:**
1. Chọn hoạt động từ danh sách
2. Xem thông tin chi tiết:
   - Tên hoạt động
   - Loại hoạt động
   - Mô tả
   - Thời gian, địa điểm
   - Số lượng tham gia
   - Trạng thái đăng ký của mình
3. Thực hiện hành động (nếu có):
   - Đăng ký tham gia
   - Hủy đăng ký
   - Xem danh sách người tham gia

---

### 2.11. UC10: Xem điểm hoạt động

**Mô tả:** Xem điểm hoạt động của mình.

**Luồng chính:**
1. Truy cập trang cá nhân hoặc "CLB của tôi"
2. Xem điểm hoạt động tổng hợp
3. Xem chi tiết điểm theo từng hoạt động
4. Xem lịch sử tích lũy điểm

---

### 2.12. UC11: Xem thông báo

**Mô tả:** Xem các thông báo từ CLB và hệ thống.

**Luồng chính:**
1. Truy cập "Thông báo" hoặc xem biểu tượng thông báo
2. Xem danh sách thông báo
3. Đánh dấu đã đọc
4. Xem chi tiết thông báo

**Loại thông báo:**
- Phê duyệt/từ chối đăng ký CLB
- Phê duyệt/từ chối đăng ký hoạt động
- Phê duyệt/từ chối đề xuất hoạt động
- Thông báo về vi phạm và kỷ luật
- Thông báo hoạt động mới

---

### 2.13. UC12: Xem sự kiện sắp tới

**Mô tả:** Xem danh sách các sự kiện/hoạt động sắp tới của CLB.

**Luồng chính:**
1. Truy cập "CLB của tôi" → Tab "Sự kiện"
2. Xem danh sách sự kiện với các tab:
   - Đang diễn ra
   - Sắp tới
   - Đã kết thúc
3. Lọc theo:
   - Từ khóa
   - Trạng thái đăng ký
   - Loại hoạt động
4. Xem chi tiết sự kiện

---

### 2.14. UC13: Xem hồ sơ cá nhân

**Mô tả:** Xem và cập nhật thông tin cá nhân.

**Luồng chính:**
1. Truy cập "Hồ sơ" hoặc avatar
2. Xem thông tin cá nhân:
   - Họ tên
   - MSSV
   - Email
   - CLB đang tham gia
   - Chức vụ trong CLB
3. Cập nhật thông tin (nếu được phép)
4. Đổi mật khẩu

---

### 2.15. UC14: Đổi mật khẩu

**Mô tả:** Thay đổi mật khẩu tài khoản.

**Luồng chính:**
1. Truy cập "Đổi mật khẩu" từ menu avatar
2. Nhập mật khẩu hiện tại
3. Nhập mật khẩu mới
4. Xác nhận mật khẩu mới
5. Lưu thay đổi

**Điều kiện sau:** Mật khẩu được cập nhật, cần đăng nhập lại

---

### 2.16. UC15: Xem lịch sử vi phạm (chỉ xem)

**Mô tả:** Xem lịch sử vi phạm và kỷ luật của mình (nếu có).

**Luồng chính:**
1. Truy cập phần thông tin cá nhân
2. Xem lịch sử vi phạm (nếu có)
3. Xem chi tiết từng vi phạm:
   - Nội quy vi phạm
   - Mô tả vi phạm
   - Mức độ
   - Hình thức kỷ luật
   - Lý do kỷ luật
   - Thời gian

**Lưu ý:** Chỉ xem, không được chỉnh sửa

---

## 3. PHÂN QUYỀN CHI TIẾT

### 3.1. Bảng phân quyền chức năng

| Chức năng | Thành viên | Thư ký | Trưởng ban | Phó CN | Chủ nhiệm |
|-----------|------------|--------|------------|--------|-----------|
| Xem trang chủ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Đăng ký CLB | ✅ | - | - | - | - |
| Đăng ký hoạt động | ✅ | ✅ | ✅ | ✅ | ✅ |
| Đề xuất hoạt động | ✅ | ✅ | ✅ | ✅ | ✅ |
| Xem danh sách thành viên | ✅ | ✅ | ✅ | ✅ | ✅ |
| Thêm/xóa thành viên | - | - | - | ⚠️ | ✅ |
| Phê duyệt đơn đăng ký CLB | - | - | - | ✅ | ✅ |
| Phân quyền/Gán chức vụ | - | - | - | - | ✅ |
| Tạo hoạt động | - | - | - | ✅ | ✅ |
| Duyệt đăng ký hoạt động | - | - | - | ✅ | ✅ |
| Duyệt đề xuất hoạt động | - | - | - | ✅ | ✅ |
| Quản lý điểm hoạt động | - | - | - | ✅ | ✅ |
| Xem thống kê | ✅ | ✅ | ✅ | ✅ | ✅ |
| Xuất báo cáo | ✅ | ✅ | ✅ | ✅ | ✅ |
| Ghi nhận vi phạm | - | - | - | ✅ | ✅ |
| Xem lịch sử kỷ luật | ✅ | ✅ | ✅ | ✅ | ✅ |
| Cập nhật thông tin CLB | - | - | - | - | ✅ |

**Chú thích:**
- ✅ = Có quyền
- ⚠️ = Có quyền hạn chế (cần xác nhận)
- - = Không có quyền

---

## 4. ĐIỀU KIỆN VÀ RÀNG BUỘC

### 4.1. Điều kiện chung
- Tất cả người dùng phải đăng nhập để sử dụng hệ thống
- Thông tin cá nhân phải được xác thực

### 4.2. Ràng buộc nghiệp vụ
- Một sinh viên chỉ có thể đăng ký tham gia một CLB tại một thời điểm
- Phó Chủ nhiệm có thể thực hiện hầu hết chức năng của Chủ nhiệm, nhưng Chủ nhiệm có quyền tối cao
- Vi phạm đã được xử lý không thể chỉnh sửa hoặc xóa
- Lịch sử kỷ luật chỉ được xem, không được chỉnh sửa

### 4.3. Xử lý ngoại lệ
- Nếu đơn đăng ký bị từ chối, sinh viên vẫn có thể đăng ký lại sau
- Nếu hoạt động bị hủy, các đơn đăng ký sẽ tự động bị hủy
- Nếu vi phạm nghiêm trọng, có thể được đề xuất lên Admin để xử lý kỷ luật cấp trường

---

## 5. ĐỊNH NGHĨA THUẬT NGỮ

- **CLB (Câu lạc bộ):** Tổ chức sinh viên trong trường
- **Hoạt động:** Sự kiện, chương trình được CLB tổ chức
- **Đề xuất hoạt động:** Ý tưởng hoạt động do thành viên đề xuất, chờ phê duyệt
- **Vi phạm:** Hành vi vi phạm nội quy của thành viên
- **Kỷ luật:** Hình thức xử phạt áp dụng cho vi phạm
- **Điểm hoạt động:** Điểm tích lũy từ việc tham gia hoạt động
- **Phê duyệt:** Quá trình xác nhận và cho phép một yêu cầu/đề xuất

---

## 6. SƠ ĐỒ USE CASE

Xem file `DIAGRAMS_USE_CASE.md` để xem các sơ đồ Use Case chi tiết bao gồm:
- Sơ đồ Use Case tổng quát
- Sơ đồ Use Case Admin
- Sơ đồ Use Case Sinh viên - Quản lý CLB
- Sơ đồ Use Case Sinh viên - Thành viên
- Các sơ đồ chi tiết khác

