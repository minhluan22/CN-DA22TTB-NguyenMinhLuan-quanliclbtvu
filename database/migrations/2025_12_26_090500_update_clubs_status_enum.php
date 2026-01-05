<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cập nhật tất cả CLB có status = 'pending' thành 'active'
        DB::table('clubs')
            ->where('status', 'pending')
            ->update(['status' => 'active']);

        // Thay đổi enum để chỉ còn active và archived
        DB::statement("ALTER TABLE `clubs` MODIFY COLUMN `status` ENUM('active', 'archived') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục lại enum cũ
        DB::statement("ALTER TABLE `clubs` MODIFY COLUMN `status` ENUM('active', 'pending', 'archived') NOT NULL DEFAULT 'pending'");
    }
};

