<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateActivityLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📍 Cập nhật địa điểm tổ chức cho các hoạt động...');

        // Danh sách địa điểm mẫu
        $locations = [
            'Giảng đường A1 - Trường Đại học Trà Vinh',
            'Giảng đường A2 - Trường Đại học Trà Vinh',
            'Giảng đường B1 - Trường Đại học Trà Vinh',
            'Giảng đường B2 - Trường Đại học Trà Vinh',
            'Giảng đường C1 - Trường Đại học Trà Vinh',
            'Giảng đường C2 - Trường Đại học Trà Vinh',
            'Giảng đường D1 - Trường Đại học Trà Vinh',
            'Giảng đường D2 - Trường Đại học Trà Vinh',
            'Giảng đường D3 - Trường Đại học Trà Vinh',
            'Giảng đường D4 - Trường Đại học Trà Vinh',
            'Giảng đường D5 - Trường Đại học Trà Vinh',
            'Hội trường lớn - Trường Đại học Trà Vinh',
            'Hội trường nhỏ - Trường Đại học Trà Vinh',
            'Sân thể thao - Trường Đại học Trà Vinh',
            'Sân bóng đá - Trường Đại học Trà Vinh',
            'Sân bóng chuyền - Trường Đại học Trà Vinh',
            'Sân cầu lông - Trường Đại học Trà Vinh',
            'Thư viện - Trường Đại học Trà Vinh',
            'Phòng họp CLB - Trường Đại học Trà Vinh',
            'Khu vực ngoài trời - Trường Đại học Trà Vinh',
            'Trung tâm Văn hóa - Trường Đại học Trà Vinh',
            'Ký túc xá - Trường Đại học Trà Vinh',
        ];

        // Cập nhật các hoạt động có location null hoặc "Địa điểm tổ chức"
        $events = DB::table('events')
            ->where(function($query) {
                $query->whereNull('location')
                      ->orWhere('location', '=', '')
                      ->orWhere('location', 'like', '%Địa điểm tổ chức%');
            })
            ->get();

        $updated = 0;
        foreach ($events as $event) {
            $randomLocation = $locations[array_rand($locations)];
            DB::table('events')
                ->where('id', $event->id)
                ->update([
                    'location' => $randomLocation,
                    'updated_at' => Carbon::now()
                ]);
            $updated++;
        }

        $this->command->info("✅ Đã cập nhật địa điểm cho {$updated} hoạt động.");
    }
}

