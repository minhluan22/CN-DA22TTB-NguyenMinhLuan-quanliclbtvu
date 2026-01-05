<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class AddMoreStudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu tạo thêm 500 sinh viên...');

        // Lấy MSSV cao nhất hiện tại để tiếp tục
        $maxMSSV = User::where('role_id', 2)
            ->whereNotNull('student_code')
            ->where('student_code', 'regexp', '^11[0-9]{7}$')
            ->orderBy('student_code', 'desc')
            ->value('student_code');

        // Phân tích MSSV cao nhất
        if ($maxMSSV) {
            $lastYear = (int)substr($maxMSSV, 4, 2);
            $lastNumber = (int)substr($maxMSSV, 6, 3);
        } else {
            $lastYear = 25; // Khóa 2025
            $lastNumber = 0;
        }

        $genders = ['male', 'female'];
        $added = 0;
        $currentYear = $lastYear;
        $currentNumber = $lastNumber + 1;

        // Tạo 500 sinh viên
        for ($i = 0; $i < 500; $i++) {
            // Nếu số thứ tự vượt quá 999, chuyển sang năm tiếp theo
            if ($currentNumber > 999) {
                $currentYear++;
                $currentNumber = 1;
                
                // Nếu năm vượt quá 25 (2025), quay lại năm 20 (2020) để tạo đa dạng
                if ($currentYear > 25) {
                    $currentYear = 20;
                }
            }

            // Tạo MSSV: 11|XX|YY|NNN
            // 11: Hệ đào tạo
            // XX: Mã khoa (01-12, random để tạo đa dạng)
            // YY: Khóa (20-25)
            // NNN: Số thứ tự (001-999)
            
            $facultyCode = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
            $yearCode = str_pad($currentYear, 2, '0', STR_PAD_LEFT);
            $numberCode = str_pad($currentNumber, 3, '0', STR_PAD_LEFT);
            
            $mssv = '11' . $facultyCode . $yearCode . $numberCode;
            
            // Kiểm tra xem đã tồn tại chưa
            $exists = User::where('student_code', $mssv)->exists();
            if ($exists) {
                $currentNumber++;
                continue;
            }

            $gender = $genders[array_rand($genders)];
            $firstName = $this->getRandomVietnameseName($gender);
            $lastName = $this->getRandomVietnameseLastName();
            $fullName = $lastName . ' ' . $firstName;

            // Tạo email theo format: MSSV@st.tvu.edu.vn
            $email = $mssv . '@st.tvu.edu.vn';

            // Tạo năm học
            $academicYear = 2000 + $currentYear;

            User::create([
                'name' => $fullName,
                'student_code' => $mssv,
                'email' => $email,
                'password' => Hash::make('123456'), // Mật khẩu mặc định
                'role_id' => 2, // Student
                'status' => 1,
                'gender' => $gender,
                'date_of_birth' => Carbon::now()->subYears(rand(18, 23))->subMonths(rand(0, 11))->subDays(rand(0, 30)),
                'phone' => $this->generatePhoneNumber(),
                'created_at' => Carbon::create($academicYear, 9, 1)->addDays(rand(0, 30)),
                'updated_at' => now(),
            ]);

            $added++;
            $currentNumber++;

            if ($added % 50 == 0) {
                $this->command->info("  Đã tạo {$added} sinh viên...");
            }
        }

        $this->command->info("✅ Đã tạo {$added} sinh viên mới.");
        $this->command->info("💡 Vui lòng chạy: php artisan db:seed --class=UpdateStudentInfoSeeder để cập nhật thông tin khoa/lớp cho các sinh viên mới.");
    }

    /**
     * Tạo số điện thoại hợp lệ
     */
    private function generatePhoneNumber()
    {
        $prefixes = ['032', '033', '034', '035', '036', '037', '038', '039', '070', '076', '077', '078', '079', '081', '082', '083', '084', '085', '086', '087', '088', '089', '090', '091', '092', '093', '094', '096', '097', '098'];
        $prefix = $prefixes[array_rand($prefixes)];
        $number = rand(1000000, 9999999);
        return $prefix . $number;
    }

    /**
     * Tạo tên tiếng Việt ngẫu nhiên
     */
    private function getRandomVietnameseName($gender)
    {
        if ($gender === 'female') {
            $names = ['Lan', 'Hương', 'Mai', 'Linh', 'Anh', 'Hoa', 'Nga', 'Phương', 'Thảo', 'Vy', 'Trang', 'Uyên', 'Yến', 'Nhi', 'My', 'Ly', 'Di', 'Giang', 'Hằng', 'Hạnh', 'Hạ', 'Hà', 'Hải', 'Hồng', 'Huệ', 'Khuê', 'Kiều', 'Liên', 'Loan', 'Mỹ', 'Ngân', 'Ngọc', 'Nhung', 'Oanh', 'Phượng', 'Quỳnh', 'Tâm', 'Thanh', 'Thúy', 'Thư', 'Thương', 'Trinh', 'Tuyết', 'Vân', 'Xuân'];
        } else {
            $names = ['Anh', 'Bảo', 'Cường', 'Dũng', 'Đức', 'Giang', 'Hải', 'Hoàng', 'Hùng', 'Khánh', 'Linh', 'Long', 'Minh', 'Nam', 'Phong', 'Quang', 'Sơn', 'Tài', 'Thành', 'Thắng', 'Tuấn', 'Việt', 'Vinh', 'Vũ', 'An', 'Bình', 'Chiến', 'Dương', 'Hậu', 'Hiếu', 'Khang', 'Kiên', 'Lâm', 'Mạnh', 'Nhân', 'Phú', 'Quốc', 'Sang', 'Tâm', 'Thái', 'Thiện', 'Trí', 'Trung', 'Tú', 'Văn'];
        }
        
        return $names[array_rand($names)];
    }

    /**
     * Tạo họ tiếng Việt ngẫu nhiên
     */
    private function getRandomVietnameseLastName()
    {
        $lastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Đinh', 'Đào', 'Chu', 'Mai', 'Tạ', 'Tăng', 'Thái', 'Thi', 'Thân', 'Tô', 'Tôn', 'Trịnh', 'Vương', 'Vi'];
        return $lastNames[array_rand($lastNames)];
    }
}

