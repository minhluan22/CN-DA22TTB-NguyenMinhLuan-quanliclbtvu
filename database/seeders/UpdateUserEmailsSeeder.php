<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUserEmailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Cập nhật email cho tất cả user theo format: mssv@st.tvu.edu.vn
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu cập nhật email theo MSSV (format: MSSV@st.tvu.edu.vn)...');

        $users = User::whereNotNull('student_code')
                    ->where('student_code', '<>', '')
                    ->get();

        $updated = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $newEmail = $user->student_code . '@st.tvu.edu.vn';
            
            // Kiểm tra email mới đã tồn tại chưa (trừ chính user hiện tại)
            $emailExists = User::where('email', $newEmail)
                             ->where('id', '!=', $user->id)
                             ->exists();

            if ($emailExists) {
                $this->command->warn("⚠️  Email {$newEmail} đã tồn tại, bỏ qua user ID: {$user->id}");
                $skipped++;
                continue;
            }

            $user->email = $newEmail;
            $user->save();
            $updated++;
        }

        $this->command->info("✅ Đã cập nhật {$updated} email thành công!");
        if ($skipped > 0) {
            $this->command->warn("⚠️  Đã bỏ qua {$skipped} user do email trùng.");
        }
    }
}

