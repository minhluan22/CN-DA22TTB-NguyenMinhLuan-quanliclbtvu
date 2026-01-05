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
        Schema::table('clubs', function (Blueprint $table) {
            // Thêm trường mục tiêu hoạt động
            $table->text('activity_goals')->nullable()->after('description');
        });

        // Đổi club_type từ enum sang string
        DB::statement("ALTER TABLE clubs MODIFY COLUMN club_type VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn('activity_goals');
        });
        
        // Khôi phục lại enum (có thể cần điều chỉnh tùy theo dữ liệu)
        DB::statement("ALTER TABLE clubs MODIFY COLUMN club_type ENUM('academic', 'arts', 'sports', 'volunteer', 'other') NULL");
    }
};
