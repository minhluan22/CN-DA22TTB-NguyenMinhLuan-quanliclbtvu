<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            // A. Thông tin cơ bản
            $table->enum('club_type', ['academic', 'arts', 'sports', 'volunteer', 'other'])->nullable()->after('field'); // Loại hình CLB
            $table->date('establishment_date')->nullable()->after('description'); // Ngày thành lập
            $table->string('banner')->nullable()->after('logo'); // Banner giới thiệu
            
            // F. Liên hệ & thông tin hiển thị
            $table->string('email')->nullable()->after('field');
            $table->string('fanpage')->nullable()->after('email');
            $table->string('phone')->nullable()->after('fanpage');
            $table->text('social_links')->nullable()->after('phone'); // JSON: Facebook, Discord, Zalo
            $table->string('meeting_place')->nullable()->after('social_links'); // Nơi sinh hoạt
            $table->string('meeting_schedule')->nullable()->after('meeting_place'); // Lịch sinh hoạt cố định
            
            // G. Cài đặt nâng cao
            $table->enum('approval_mode', ['auto', 'manual'])->default('manual')->after('status'); // Chế độ duyệt thành viên
            $table->enum('activity_approval_mode', ['school', 'chairman'])->default('school')->after('approval_mode'); // Chế độ duyệt hoạt động
            $table->boolean('is_public')->default(true)->after('activity_approval_mode'); // Ẩn/Hiện trên website
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            //
        });
    }
};
