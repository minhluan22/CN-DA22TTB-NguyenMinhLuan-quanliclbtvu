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
        Schema::table('events', function (Blueprint $table) {
            // Loại vi phạm (text để linh hoạt)
            $table->string('violation_type')->nullable()->after('violation_notes')->comment('Loại vi phạm');
            
            // Mức độ vi phạm: Nhẹ / Trung bình / Nghiêm trọng
            $table->enum('violation_severity', ['light', 'medium', 'serious'])->nullable()->after('violation_type')->comment('Mức độ vi phạm');
            
            // Trạng thái xử lý vi phạm: Chưa xử lý / Đang xử lý / Đã xử lý
            $table->enum('violation_status', ['pending', 'processing', 'processed'])->nullable()->after('violation_severity')->comment('Trạng thái xử lý vi phạm');
            
            // Ngày phát hiện vi phạm
            $table->timestamp('violation_detected_at')->nullable()->after('violation_status')->comment('Ngày phát hiện vi phạm');
            
            // Người ghi nhận vi phạm (Admin)
            $table->unsignedBigInteger('violation_recorded_by')->nullable()->after('violation_detected_at')->comment('Admin ghi nhận vi phạm');
            
            $table->foreign('violation_recorded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['violation_recorded_by']);
            $table->dropColumn([
                'violation_type',
                'violation_severity',
                'violation_status',
                'violation_detected_at',
                'violation_recorded_by'
            ]);
        });
    }
};
