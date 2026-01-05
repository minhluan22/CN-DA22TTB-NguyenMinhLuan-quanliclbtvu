<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class UpdateStudentInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu cập nhật thông tin tài khoản sinh viên...');

        // Mapping mã đơn vị (02) -> Khoa và Ngành
        $facultyMapping = [
            '01' => [
                'department' => 'Khoa Công nghệ Thông tin',
                'major' => 'Công nghệ thông tin',
                'major_code' => 'TT',
                'majors' => ['Công nghệ thông tin', 'Trí tuệ nhân tạo', 'Mạng máy tính và truyền thông dữ liệu', 'Hệ thống thông tin quản lý']
            ],
            '02' => [
                'department' => 'Khoa Công nghệ Thông tin',
                'major' => 'Công nghệ thông tin',
                'major_code' => 'TT',
                'majors' => ['Công nghệ thông tin', 'Trí tuệ nhân tạo', 'Mạng máy tính và truyền thông dữ liệu', 'Hệ thống thông tin quản lý']
            ],
            '03' => [
                'department' => 'Khoa Kỹ thuật và Công nghệ',
                'major' => 'Công nghệ kỹ thuật ô tô',
                'major_code' => 'OT',
                'majors' => ['Công nghệ kỹ thuật ô tô', 'Kỹ thuật xây dựng công trình giao thông', 'Kỹ thuật môi trường', 'Công nghệ kỹ thuật hóa học']
            ],
            '04' => [
                'department' => 'Khoa Kinh tế - Luật',
                'major' => 'Quản trị kinh doanh',
                'major_code' => 'QK',
                'majors' => ['Quản trị kinh doanh', 'Kế toán', 'Thương mại điện tử', 'Logistics và Quản lý chuỗi cung ứng', 'Kinh tế']
            ],
            '05' => [
                'department' => 'Khoa Kinh tế - Luật',
                'major' => 'Luật học',
                'major_code' => 'LU',
                'majors' => ['Luật học', 'Chính trị học', 'Quản lý Nhà nước']
            ],
            '06' => [
                'department' => 'Khoa Nông nghiệp - Thủy sản',
                'major' => 'Nông nghiệp',
                'major_code' => 'NN',
                'majors' => ['Nông nghiệp', 'Nuôi trồng thủy sản', 'Bảo vệ thực vật', 'Thú y', 'Chăn nuôi']
            ],
            '07' => [
                'department' => 'Khoa Nông nghiệp - Thủy sản',
                'major' => 'Công nghệ thực phẩm',
                'major_code' => 'TP',
                'majors' => ['Công nghệ thực phẩm', 'Quản lý tài nguyên và môi trường']
            ],
            '08' => [
                'department' => 'Khoa Y Dược',
                'major' => 'Y khoa',
                'major_code' => 'YK',
                'majors' => ['Y khoa', 'Dược học', 'Điều dưỡng', 'Y học dự phòng', 'Y tế công cộng', 'Kỹ thuật xét nghiệm y học', 'Kỹ thuật hình ảnh y học', 'Kỹ thuật phục hồi chức năng', 'Răng - Hàm - Mặt']
            ],
            '09' => [
                'department' => 'Khoa Ngoại ngữ',
                'major' => 'Ngôn ngữ Anh',
                'major_code' => 'NA',
                'majors' => ['Ngôn ngữ Anh', 'Ngôn ngữ Trung Quốc', 'Ngôn ngữ Khmer']
            ],
            '10' => [
                'department' => 'Khoa Khoa học Xã hội và Nhân văn',
                'major' => 'Văn hóa học',
                'major_code' => 'VH',
                'majors' => ['Văn hóa học', 'Âm nhạc học', 'Công tác xã hội', 'Quản trị văn phòng']
            ],
            '11' => [
                'department' => 'Khoa Khoa học Xã hội và Nhân văn',
                'major' => 'Quản trị dịch vụ du lịch và lữ hành',
                'major_code' => 'DL',
                'majors' => ['Quản trị dịch vụ du lịch và lữ hành', 'Quản lý thể dục thể thao']
            ],
            '12' => [
                'department' => 'Khoa Giáo dục và Sư phạm',
                'major' => 'Giáo dục Tiểu học',
                'major_code' => 'GD',
                'majors' => ['Giáo dục Tiểu học', 'Giáo dục Mầm non']
            ],
        ];

        // Lấy tất cả sinh viên (role_id = 2) và sắp xếp theo MSSV để chia đều
        $students = User::where('role_id', 2)
            ->whereNotNull('student_code')
            ->where('student_code', '!=', '')
            ->orderBy('student_code')
            ->get();
        
        $updated = 0;
        $skipped = 0;
        
        // Đếm số lượng ngành tổng cộng để chia đều
        $allMajors = [];
        foreach ($facultyMapping as $code => $info) {
            foreach ($info['majors'] as $major) {
                $allMajors[] = [
                    'major' => $major,
                    'department' => $info['department'],
                    'major_code' => $this->getMajorCode($major, $info['major_code']),
                ];
            }
        }
        $totalMajors = count($allMajors);
        
        $this->command->info("📊 Tổng số ngành: {$totalMajors}");
        $this->command->info("📊 Tổng số sinh viên: {$students->count()}");

        foreach ($students as $index => $student) {
            if (empty($student->student_code) || strlen($student->student_code) < 9) {
                $skipped++;
                continue;
            }

            $mssv = $student->student_code;
            
            // Phân tích MSSV: 110222109
            // Vị trí: 0-1: 11 (Hệ đào tạo - bỏ qua)
            // Vị trí: 2-3: 02 (Mã đơn vị/khoa)
            // Vị trí: 4-5: 22 (Khóa tuyển sinh)
            // Vị trí: 6-8: 109 (Số thứ tự)
            
            $facultyCode = substr($mssv, 2, 2); // Lấy 2 ký tự từ vị trí 2 (02)
            $yearCode = substr($mssv, 4, 2); // Lấy 2 ký tự từ vị trí 4 (22)
            $studentNumber = substr($mssv, 6, 3); // Lấy 3 ký tự cuối (109)
            
            // Chuyển đổi năm: 20 -> 2020, 22 -> 2022, 25 -> 2025
            $academicYear = 2000 + (int)$yearCode;
            
            // CHIA ĐỀU: 80% sinh viên theo mã khoa từ MSSV, 20% chia đều vào tất cả ngành
            $useFacultyCode = (($index % 10) < 8); // 80% dùng mã khoa
            
            if ($useFacultyCode && isset($facultyMapping[$facultyCode])) {
                // 80%: Dùng mã khoa từ MSSV, chọn ngành trong khoa đó dựa trên số thứ tự để chia đều
                $facultyInfo = $facultyMapping[$facultyCode];
                $majorIndex = ((int)$studentNumber - 1) % count($facultyInfo['majors']);
                $major = $facultyInfo['majors'][$majorIndex];
                $majorCode = $this->getMajorCode($major, $facultyInfo['major_code']);
                $department = $facultyInfo['department'] . ' - ' . $major;
            } else {
                // 20%: Chia đều vào tất cả ngành (lộn xộn - để tạo tính đa dạng)
                $majorIndex = $index % $totalMajors;
                $majorInfo = $allMajors[$majorIndex];
                $major = $majorInfo['major'];
                $majorCode = $majorInfo['major_code'];
                $department = $majorInfo['department'] . ' - ' . $major;
            }
            
            // Tạo mã lớp: DA|22|TT|B
            // DA: Đại học
            // 22: Khóa
            // TT: Mã ngành
            // B: Phân lớp (A, B, C, D, E) dựa trên số thứ tự
            $classLetter = $this->getClassLetter($studentNumber);
            $classCode = 'DA' . $yearCode . $majorCode . $classLetter;
            
            // Cập nhật thông tin
            $updateData = [
                // Giữ nguyên: name, student_code, email
                
                // Cập nhật số điện thoại (format: 0XXX XXX XXXX)
                'phone' => $this->generatePhoneNumber(),
                
                // Cập nhật giới tính (dựa trên tên hoặc random)
                'gender' => $this->determineGender($student->name),
                
                // Cập nhật ngày sinh (18-23 tuổi, sinh vào năm trước khóa học 2-4 năm)
                'date_of_birth' => $this->generateBirthDate($academicYear),
                
                // Cập nhật khoa - ngành học
                'department' => $department,
                
                // Cập nhật lớp
                'class' => $classCode,
                
                // Cập nhật giới thiệu bản thân
                'bio' => $this->generateBio($student->name, $major, $academicYear),
                
                'updated_at' => now(),
            ];
            
            $student->update($updateData);
            $updated++;
            
            if ($updated % 50 == 0) {
                $this->command->info("  Đã cập nhật {$updated} sinh viên...");
            }
        }

        $this->command->info("✅ Đã cập nhật thông tin cho {$updated} sinh viên.");
        if ($skipped > 0) {
            $this->command->warn("⚠️  Bỏ qua {$skipped} sinh viên (không có MSSV hợp lệ).");
        }
    }

    /**
     * Lấy mã ngành từ tên ngành
     */
    private function getMajorCode($major, $defaultCode)
    {
        $majorCodeMap = [
            'Công nghệ thông tin' => 'TT',
            'Trí tuệ nhân tạo' => 'AI',
            'Mạng máy tính và truyền thông dữ liệu' => 'MT',
            'Hệ thống thông tin quản lý' => 'HT',
            'Công nghệ kỹ thuật ô tô' => 'OT',
            'Kỹ thuật xây dựng công trình giao thông' => 'XD',
            'Kỹ thuật môi trường' => 'MT',
            'Công nghệ kỹ thuật hóa học' => 'HC',
            'Quản trị kinh doanh' => 'QK',
            'Kế toán' => 'KT',
            'Thương mại điện tử' => 'TM',
            'Logistics và Quản lý chuỗi cung ứng' => 'LG',
            'Kinh tế' => 'KT',
            'Luật học' => 'LU',
            'Chính trị học' => 'CT',
            'Quản lý Nhà nước' => 'QL',
            'Nông nghiệp' => 'NN',
            'Nuôi trồng thủy sản' => 'TS',
            'Bảo vệ thực vật' => 'BV',
            'Thú y' => 'TY',
            'Chăn nuôi' => 'CN',
            'Công nghệ thực phẩm' => 'TP',
            'Quản lý tài nguyên và môi trường' => 'TN',
            'Y khoa' => 'YK',
            'Dược học' => 'DU',
            'Điều dưỡng' => 'DD',
            'Y học dự phòng' => 'YP',
            'Y tế công cộng' => 'YT',
            'Kỹ thuật xét nghiệm y học' => 'XN',
            'Kỹ thuật hình ảnh y học' => 'HA',
            'Kỹ thuật phục hồi chức năng' => 'PH',
            'Răng - Hàm - Mặt' => 'RH',
            'Ngôn ngữ Anh' => 'NA',
            'Ngôn ngữ Trung Quốc' => 'NT',
            'Ngôn ngữ Khmer' => 'NK',
            'Văn hóa học' => 'VH',
            'Âm nhạc học' => 'AM',
            'Công tác xã hội' => 'XH',
            'Quản trị văn phòng' => 'VP',
            'Quản trị dịch vụ du lịch và lữ hành' => 'DL',
            'Quản lý thể dục thể thao' => 'TD',
            'Giáo dục Tiểu học' => 'GD',
            'Giáo dục Mầm non' => 'MN',
        ];
        
        return $majorCodeMap[$major] ?? $defaultCode;
    }

    /**
     * Xác định chữ cái lớp dựa trên số thứ tự
     */
    private function getClassLetter($studentNumber)
    {
        $number = (int)$studentNumber;
        
        // Phân chia: 1-40 = A, 41-80 = B, 81-120 = C, 121-160 = D, 161+ = E
        if ($number <= 40) return 'A';
        if ($number <= 80) return 'B';
        if ($number <= 120) return 'C';
        if ($number <= 160) return 'D';
        return 'E';
    }

    /**
     * Xác định giới tính từ tên
     */
    private function determineGender($name)
    {
        // Tên nữ thường có: Thị, Thị, Lan, Hương, Mai, Linh, Anh, Hoa, Nga, Phương, Thảo, Vy, Trang, Uyên, Yến, Nhi, My, Ly, Di, Giang, Hằng, Hạnh, Hạ, Hà, Hải, Hoa, Hồng, Huệ, Hương, Khuê, Kiều, Lan, Liên, Linh, Loan, Mai, Mỹ, Nga, Ngân, Ngọc, Nhung, Nhung, Oanh, Phượng, Phương, Quỳnh, Tâm, Thanh, Thảo, Thúy, Thư, Thương, Trang, Trinh, Tuyết, Uyên, Vân, Vy, Xuân, Yến
        $femaleIndicators = ['Thị', 'Lan', 'Hương', 'Mai', 'Linh', 'Anh', 'Hoa', 'Nga', 'Phương', 'Thảo', 'Vy', 'Trang', 'Uyên', 'Yến', 'Nhi', 'My', 'Ly', 'Di', 'Giang', 'Hằng', 'Hạnh', 'Hạ', 'Hà', 'Hải', 'Hồng', 'Huệ', 'Khuê', 'Kiều', 'Liên', 'Loan', 'Mỹ', 'Ngân', 'Ngọc', 'Nhung', 'Oanh', 'Phượng', 'Quỳnh', 'Tâm', 'Thanh', 'Thúy', 'Thư', 'Thương', 'Trinh', 'Tuyết', 'Vân', 'Xuân'];
        
        foreach ($femaleIndicators as $indicator) {
            if (stripos($name, $indicator) !== false) {
                return 'female';
            }
        }
        
        // Mặc định random nếu không xác định được
        return rand(0, 1) == 0 ? 'male' : 'female';
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
     * Tạo ngày sinh hợp lý
     */
    private function generateBirthDate($academicYear)
    {
        // Sinh viên thường 18-23 tuổi khi nhập học
        // Nếu khóa 2022, sinh vào năm 1999-2004
        $minYear = $academicYear - 23;
        $maxYear = $academicYear - 18;
        
        $year = rand($minYear, $maxYear);
        $month = rand(1, 12);
        $day = rand(1, 28); // Tránh lỗi tháng 2
        
        return Carbon::create($year, $month, $day);
    }

    /**
     * Tạo giới thiệu bản thân
     */
    private function generateBio($name, $major, $academicYear)
    {
        $bios = [
            "Xin chào! Tôi là {$name}, sinh viên ngành {$major} khóa {$academicYear} tại Đại học Trà Vinh. Tôi đam mê học hỏi và phát triển bản thân.",
            "Chào mọi người! Mình là {$name}, hiện đang là sinh viên năm " . ($academicYear <= 2023 ? rand(2, 4) : 1) . " ngành {$major}. Rất vui được làm quen!",
            "{$name} - Sinh viên {$major} khóa {$academicYear}. Mong muốn được tham gia các hoạt động CLB để phát triển kỹ năng và mở rộng mối quan hệ.",
            "Xin chào! Tôi là {$name}, sinh viên Đại học Trà Vinh, chuyên ngành {$major}. Tôi thích tham gia các hoạt động ngoại khóa và tình nguyện.",
            "Chào các bạn! Mình là {$name}, sinh viên khóa {$academicYear} ngành {$major}. Mình rất thích tham gia các CLB và hoạt động của trường.",
        ];
        
        return $bios[array_rand($bios)];
    }
}

