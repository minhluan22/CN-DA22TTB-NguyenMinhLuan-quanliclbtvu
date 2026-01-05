# MÔ TẢ ERD - HỆ THỐNG QUẢN LÝ CLB

## Tổng quan

Hệ thống quản lý Câu lạc bộ Sinh viên với 14 bảng chính, quản lý người dùng, CLB, hoạt động, nội quy, vi phạm và thông báo.

---

## CÁC BẢNG DỮ LIỆU

### 1. **roles** (Vai trò)
- **Mục đích**: Quản lý vai trò trong hệ thống
- **Trường chính**: 
  - `id` (PK)
  - `name` (Admin, Student, Guest)
  - `description`
- **Quan hệ**: 
  - `1-N` với `users` (role_id)

---

### 2. **users** (Người dùng)
- **Mục đích**: Lưu thông tin tất cả người dùng (Admin, Student, Guest)
- **Trường chính**:
  - `id` (PK)
  - `name`, `email` (unique), `password`
  - `student_code` (MSSV)
  - `role_id` (FK → roles.id)
  - `status` (1: Hoạt động, 0: Khóa)
  - `class`, `phone`, `gender`, `date_of_birth`
  - `avatar`, `bio`
  - `last_activity`
  - Các cài đặt: `email_notifications`, `event_notifications`, `club_notifications`, `language`, `dark_mode`
- **Quan hệ**:
  - `N-1` với `roles` (role_id)
  - `1-N` với `clubs` (owner_id - chủ nhiệm)
  - `1-N` với `club_members`
  - `1-N` với `club_registrations`
  - `1-N` với `club_proposals` (user_id, reviewed_by)
  - `1-N` với `events` (created_by)
  - `1-N` với `event_registrations`
  - `1-N` với `regulations` (created_by, updated_by)
  - `1-N` với `violations` (user_id, recorded_by, processed_by)
  - `1-N` với `notifications` (sender_id)
  - `1-N` với `notification_recipients`
  - `1-N` với `admin_logs` (admin_id)
  - `1-N` với `support_requests` (user_id, responded_by)

---

### 3. **clubs** (Câu lạc bộ)
- **Mục đích**: Lưu thông tin các CLB
- **Trường chính**:
  - `id` (PK)
  - `name`, `slug` (unique), `code` (unique - VD: CLB001)
  - `description`, `activity_goals`
  - `logo`, `banner`
  - `owner_id` (FK → users.id - Chủ nhiệm)
  - `status` (active, pending, archived)
  - `field`, `club_type` (Lĩnh vực - lưu tiếng Anh)
  - `chairman` (Tên chủ nhiệm)
  - `establishment_date`
  - `email`, `fanpage`, `phone`
  - `social_links` (JSON)
  - `meeting_place`, `meeting_schedule`
  - `approval_mode` (auto, manual)
  - `activity_approval_mode` (school, chairman)
  - `is_public` (1: Công khai, 0: Ẩn)
- **Quan hệ**:
  - `N-1` với `users` (owner_id)
  - `1-N` với `club_members`
  - `1-N` với `club_registrations`
  - `1-N` với `events`
  - `1-N` với `regulations` (club_id - optional)
  - `1-N` với `violations`
  - `1-N` với `notifications` (club_id)
  - `1-N` với `notification_recipients` (club_id)
  - `1-N` với `support_requests` (club_id)

---

### 4. **club_members** (Thành viên CLB)
- **Mục đích**: Quan hệ nhiều-nhiều giữa users và clubs
- **Trường chính**:
  - `id` (PK)
  - `club_id` (FK → clubs.id)
  - `user_id` (FK → users.id)
  - `position` (chairman, vice_chairman, executive, member)
  - `status` (pending, approved, rejected, suspended, left)
  - `joined_date`
  - `join_count` (Số lần tham gia)
  - `notes`
- **Unique**: (club_id, user_id) - Mỗi user chỉ là thành viên 1 lần trong CLB
- **Quan hệ**:
  - `N-1` với `clubs` (club_id)
  - `N-1` với `users` (user_id)

---

### 5. **club_registrations** (Đăng ký tham gia CLB)
- **Mục đích**: Lưu đơn đăng ký tham gia CLB
- **Trường chính**:
  - `id` (PK)
  - `club_id` (FK → clubs.id)
  - `user_id` (FK → users.id)
  - `reason` (Lý do tham gia)
  - `status` (pending, approved, rejected)
- **Unique**: (club_id, user_id) - Mỗi user chỉ đăng ký 1 lần
- **Quan hệ**:
  - `N-1` với `clubs` (club_id)
  - `N-1` với `users` (user_id)

---

### 6. **club_proposals** (Đề xuất CLB mới)
- **Mục đích**: Lưu đề xuất tạo CLB mới từ sinh viên
- **Trường chính**:
  - `id` (PK)
  - `user_id` (FK → users.id - Người đề xuất)
  - `club_name`, `field`
  - `objectives`, `reason`, `planned_activities`
  - `expected_members`
  - `advisor_name`, `advisor_email`
  - `proposer_name`, `proposer_email`, `proposer_student_code`
  - `member_list_file`, `activity_plan_file`
  - `status` (pending, approved, rejected)
  - `admin_notes`
  - `reviewed_by` (FK → users.id - Admin duyệt)
  - `reviewed_at`
- **Quan hệ**:
  - `N-1` với `users` (user_id, reviewed_by)

---

### 7. **events** (Hoạt động)
- **Mục đích**: Lưu thông tin các hoạt động của CLB
- **Trường chính**:
  - `id` (PK)
  - `club_id` (FK → clubs.id)
  - `title`, `description`
  - `start_at`, `end_at`, `location`
  - `status` (upcoming, ongoing, finished, cancelled)
  - `approval_status` (pending, approved, rejected, disabled)
  - `created_by` (FK → users.id)
  - `activity_type`
  - `max_participants`
  - **Vi phạm**: `violation_type`, `violation_severity`, `violation_status`, `violation_notes`, `violation_detected_at`
  - **Đề xuất**: `proposal_type`, `proposal_reason`
  - `deleted_at` (Soft delete)
- **Quan hệ**:
  - `N-1` với `clubs` (club_id)
  - `N-1` với `users` (created_by)
  - `1-N` với `event_registrations`

---

### 8. **event_registrations** (Đăng ký tham gia hoạt động)
- **Mục đích**: Lưu đăng ký tham gia hoạt động
- **Trường chính**:
  - `id` (PK)
  - `event_id` (FK → events.id)
  - `user_id` (FK → users.id)
  - `status` (pending, approved, rejected, attended, absent)
  - `activity_points` (Điểm hoạt động)
  - `notes`
- **Unique**: (event_id, user_id) - Mỗi user chỉ đăng ký 1 lần
- **Quan hệ**:
  - `N-1` với `events` (event_id)
  - `N-1` với `users` (user_id)

---

### 9. **regulations** (Nội quy)
- **Mục đích**: Lưu các nội quy của hệ thống hoặc CLB
- **Trường chính**:
  - `id` (PK)
  - `code` (unique - Mã nội quy)
  - `title`, `content`
  - `scope` (all_clubs, specific_club)
  - `club_id` (FK → clubs.id - nullable, nếu scope = specific_club)
  - `severity` (light, medium, serious)
  - `status` (active, inactive)
  - `issued_date`
  - `created_by` (FK → users.id)
  - `updated_by` (FK → users.id)
  - `deleted_at` (Soft delete)
- **Quan hệ**:
  - `N-1` với `clubs` (club_id - optional)
  - `N-1` với `users` (created_by, updated_by)
  - `1-N` với `violations`

---

### 10. **violations** (Vi phạm)
- **Mục đích**: Lưu thông tin vi phạm nội quy
- **Trường chính**:
  - `id` (PK)
  - `user_id` (FK → users.id - Sinh viên vi phạm)
  - `club_id` (FK → clubs.id)
  - `regulation_id` (FK → regulations.id)
  - `description`
  - `severity` (light, medium, serious)
  - `violation_date`
  - `recorded_by` (FK → users.id - Người ghi nhận)
  - `status` (pending, processed, monitoring)
  - **Kỷ luật**: `discipline_type`, `discipline_reason`, `discipline_period_start`, `discipline_period_end`
  - `processed_by` (FK → users.id - Admin xử lý)
  - `processed_at`
  - `deleted_at` (Soft delete)
- **Quan hệ**:
  - `N-1` với `users` (user_id, recorded_by, processed_by)
  - `N-1` với `clubs` (club_id)
  - `N-1` với `regulations` (regulation_id)

---

### 11. **notifications** (Thông báo)
- **Mục đích**: Lưu thông báo hệ thống
- **Trường chính**:
  - `id` (PK)
  - `title`, `body`
  - `sender_id` (FK → users.id)
  - `club_id` (FK → clubs.id - nullable)
  - `type`, `source` (system, admin, club)
  - `is_public` (1: Công khai, 0: Riêng tư)
- **Quan hệ**:
  - `N-1` với `users` (sender_id)
  - `N-1` với `clubs` (club_id)
  - `1-N` với `notification_recipients`

---

### 12. **notification_recipients** (Người nhận thông báo)
- **Mục đích**: Lưu danh sách người nhận thông báo
- **Trường chính**:
  - `id` (PK)
  - `notification_id` (FK → notifications.id)
  - `user_id` (FK → users.id - nullable, nếu là CLB thì null)
  - `club_id` (FK → clubs.id - nullable, nếu là user thì null)
  - `is_read` (0: Chưa đọc, 1: Đã đọc)
  - `read_at`
- **Quan hệ**:
  - `N-1` với `notifications` (notification_id)
  - `N-1` với `users` (user_id - optional)
  - `N-1` với `clubs` (club_id - optional)

---

### 13. **admin_logs** (Nhật ký Admin)
- **Mục đích**: Ghi log các hành động của Admin
- **Trường chính**:
  - `id` (PK)
  - `admin_id` (FK → users.id)
  - `action` (Hành động)
  - `model_type`, `model_id` (Polymorphic)
  - `notes`
- **Quan hệ**:
  - `N-1` với `users` (admin_id)

---

### 14. **support_requests** (Yêu cầu hỗ trợ)
- **Mục đích**: Lưu yêu cầu hỗ trợ từ người dùng
- **Trường chính**:
  - `id` (PK)
  - `user_id` (FK → users.id)
  - `club_id` (FK → clubs.id - nullable)
  - `subject`, `message`
  - `status` (pending, in_progress, resolved, closed)
  - `response`
  - `responded_by` (FK → users.id)
  - `responded_at`
- **Quan hệ**:
  - `N-1` với `users` (user_id, responded_by)
  - `N-1` với `clubs` (club_id - optional)

---

### 15. **system_configs** (Cấu hình hệ thống)
- **Mục đích**: Lưu cấu hình hệ thống (key-value)
- **Trường chính**:
  - `id` (PK)
  - `key` (unique)
  - `value`
  - `description`
- **Không có quan hệ**

---

## SƠ ĐỒ QUAN HỆ CHÍNH

```
roles (1) ──< (N) users (role_id)
users (1) ──< (N) clubs (owner_id)
users (1) ──< (N) club_members
clubs (1) ──< (N) club_members
clubs (1) ──< (N) club_registrations
users (1) ──< (N) club_registrations
users (1) ──< (N) club_proposals (user_id, reviewed_by)
clubs (1) ──< (N) events
users (1) ──< (N) events (created_by)
events (1) ──< (N) event_registrations
users (1) ──< (N) event_registrations
clubs (1) ──< (N) regulations (optional)
users (1) ──< (N) regulations (created_by, updated_by)
regulations (1) ──< (N) violations
users (1) ──< (N) violations (user_id, recorded_by, processed_by)
clubs (1) ──< (N) violations
notifications (1) ──< (N) notification_recipients
users (1) ──< (N) notifications (sender_id)
clubs (1) ──< (N) notifications (club_id)
users (1) ──< (N) notification_recipients
clubs (1) ──< (N) notification_recipients
users (1) ──< (N) admin_logs (admin_id)
users (1) ──< (N) support_requests (user_id, responded_by)
clubs (1) ──< (N) support_requests (club_id)
```

---

## ĐẶC ĐIỂM QUAN TRỌNG

1. **Foreign Keys**: Tất cả đều có foreign key constraints với ON DELETE CASCADE/SET NULL/RESTRICT phù hợp
2. **Unique Constraints**: 
   - `users.email` (unique)
   - `clubs.slug`, `clubs.code` (unique)
   - `(club_id, user_id)` trong `club_members` (unique)
   - `(club_id, user_id)` trong `club_registrations` (unique)
   - `(event_id, user_id)` trong `event_registrations` (unique)
   - `regulations.code` (unique)
   - `system_configs.key` (unique)
3. **Soft Deletes**: `events`, `regulations`, `violations` có `deleted_at`
4. **Enum Fields**: Nhiều trường sử dụng enum để đảm bảo tính nhất quán
5. **Timestamps**: Tất cả bảng đều có `created_at`, `updated_at`

---

## CÁCH SỬ DỤNG FILE

### 1. **ERD_SCHEMA.sql**
- Import vào MySQL Workbench, phpMyAdmin, hoặc bất kỳ công cụ MySQL nào
- Sử dụng để tạo database từ đầu hoặc so sánh schema

### 2. **ERD_DBML.dbml**
- Mở tại https://dbdiagram.io
- Paste nội dung file vào editor
- Tự động vẽ ERD với quan hệ và màu sắc

### 3. **ERD_DESCRIPTION.md**
- Tài liệu mô tả chi tiết các bảng và quan hệ
- Dùng để tham khảo khi thiết kế hoặc báo cáo

