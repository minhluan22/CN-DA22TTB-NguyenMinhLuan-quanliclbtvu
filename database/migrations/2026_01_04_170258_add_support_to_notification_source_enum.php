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
        // MySQL không hỗ trợ ALTER ENUM trực tiếp, cần dùng DB::statement
        DB::statement("ALTER TABLE notifications MODIFY COLUMN notification_source ENUM('admin', 'club', 'support') DEFAULT 'admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục lại enum cũ (chỉ có admin và club)
        DB::statement("ALTER TABLE notifications MODIFY COLUMN notification_source ENUM('admin', 'club') DEFAULT 'admin'");
    }
};
