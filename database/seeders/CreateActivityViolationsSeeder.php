<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\Club;
use Carbon\Carbon;

class CreateActivityViolationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu tạo dữ liệu hoạt động vi phạm...');

        // Lấy admin user đầu tiên
        $admin = DB::table('users')->where('role_id', 1)->first();
        if (!$admin) {
            $this->command->error('Không tìm thấy Admin user!');
            return;
        }

        // Các loại vi phạm phổ biến
        $violationTypes = [
            'Tổ chức không đúng nội dung đã đăng ký',
            'Vi phạm nội quy CLB',
            'Vi phạm nội quy nhà trường',
            'Không xin phép nhưng vẫn tổ chức',
            'Tổ chức sai thời gian/địa điểm',
            'Có phản ánh từ sinh viên',
            'Nội dung không phù hợp',
            'Vi phạm quy định về tài chính',
        ];

        // Lấy một số hoạt động ngẫu nhiên để đánh dấu vi phạm (khoảng 10-15%)
        $events = Event::whereNull('violation_notes')
            ->whereNull('violation_status')
            ->inRandomOrder()
            ->take(rand(12, 18))
            ->get();

        $created = 0;
        $severityWeights = ['light' => 40, 'medium' => 45, 'serious' => 15]; // Tỷ lệ mức độ
        $statusWeights = ['pending' => 50, 'processing' => 30, 'processed' => 20]; // Tỷ lệ trạng thái

        foreach ($events as $event) {
            // Chọn mức độ vi phạm theo trọng số
            $severityRand = rand(1, 100);
            $violationSeverity = 'medium';
            if ($severityRand <= $severityWeights['light']) {
                $violationSeverity = 'light';
            } elseif ($severityRand <= $severityWeights['light'] + $severityWeights['medium']) {
                $violationSeverity = 'medium';
            } else {
                $violationSeverity = 'serious';
            }

            // Chọn trạng thái xử lý theo trọng số
            $statusRand = rand(1, 100);
            $violationStatus = 'pending';
            if ($statusRand <= $statusWeights['pending']) {
                $violationStatus = 'pending';
            } elseif ($statusRand <= $statusWeights['pending'] + $statusWeights['processing']) {
                $violationStatus = 'processing';
            } else {
                $violationStatus = 'processed';
            }

            // Tạo thời gian phát hiện vi phạm (sau khi hoạt động được tạo, nhưng không quá xa)
            $violationDetectedAt = Carbon::parse($event->created_at)->addDays(rand(1, 7));
            
            // Nếu trạng thái là processed, thời gian phát hiện nên sớm hơn
            if ($violationStatus == 'processed') {
                $violationDetectedAt = Carbon::parse($event->created_at)->addDays(rand(1, 3));
            }

            // Chọn loại vi phạm ngẫu nhiên
            $violationType = $violationTypes[array_rand($violationTypes)];

            // Tạo ghi chú vi phạm chi tiết
            $violationNotes = $this->generateViolationNote($violationType, $violationSeverity, $event);

            // Cập nhật event
            $event->update([
                'violation_type' => $violationType,
                'violation_severity' => $violationSeverity,
                'violation_status' => $violationStatus,
                'violation_notes' => $violationNotes,
                'violation_detected_at' => $violationDetectedAt,
                'violation_recorded_by' => $admin->id,
                'status' => $violationSeverity == 'serious' ? 'disabled' : $event->status, // Nghiêm trọng thì vô hiệu hóa
            ]);

            $created++;
            $this->command->info("  Event ID {$event->id}: Đánh dấu vi phạm - {$violationType} (Mức độ: {$violationSeverity}, Trạng thái: {$violationStatus})");
        }

        $this->command->info("✅ Đã tạo {$created} hoạt động vi phạm.");
    }

    /**
     * Tạo ghi chú vi phạm chi tiết
     */
    private function generateViolationNote(string $type, string $severity, $event): string
    {
        $notes = [];
        $notes[] = "Loại vi phạm: {$type}";

        // Thêm chi tiết theo loại vi phạm
        switch ($type) {
            case 'Tổ chức không đúng nội dung đã đăng ký':
                $notes[] = "Hoạt động đã tổ chức không đúng với nội dung đã được phê duyệt ban đầu.";
                break;
            case 'Vi phạm nội quy CLB':
                $notes[] = "Hoạt động vi phạm một hoặc nhiều điều khoản trong nội quy của CLB.";
                break;
            case 'Vi phạm nội quy nhà trường':
                $notes[] = "Hoạt động vi phạm quy định của nhà trường về tổ chức hoạt động sinh viên.";
                break;
            case 'Không xin phép nhưng vẫn tổ chức':
                $notes[] = "CLB đã tổ chức hoạt động mà chưa được phê duyệt từ phía nhà trường/ban quản lý.";
                break;
            case 'Tổ chức sai thời gian/địa điểm':
                $notes[] = "Hoạt động được tổ chức không đúng với thời gian và/hoặc địa điểm đã đăng ký.";
                break;
            case 'Có phản ánh từ sinh viên':
                $notes[] = "Nhận được phản ánh từ sinh viên về các vấn đề liên quan đến hoạt động này.";
                break;
            case 'Nội dung không phù hợp':
                $notes[] = "Nội dung hoạt động không phù hợp với mục tiêu và giá trị của CLB.";
                break;
            case 'Vi phạm quy định về tài chính':
                $notes[] = "Có dấu hiệu vi phạm quy định về quản lý tài chính của hoạt động.";
                break;
        }

        // Thêm mức độ nghiêm trọng
        if ($severity == 'serious') {
            $notes[] = "Mức độ vi phạm: Nghiêm trọng - Cần xử lý ngay lập tức.";
        } elseif ($severity == 'medium') {
            $notes[] = "Mức độ vi phạm: Trung bình - Cần xem xét và xử lý.";
        } else {
            $notes[] = "Mức độ vi phạm: Nhẹ - Cảnh báo và nhắc nhở.";
        }

        // Thêm thông tin về thời gian
        $notes[] = "Thời gian phát hiện: " . now()->format('d/m/Y H:i');

        return implode("\n", $notes);
    }
}

