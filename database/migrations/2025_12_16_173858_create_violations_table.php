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
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Sinh viên vi phạm');
            $table->unsignedBigInteger('club_id')->comment('CLB vi phạm');
            $table->unsignedBigInteger('regulation_id')->comment('Nội quy bị vi phạm');
            $table->text('description')->comment('Mô tả hành vi vi phạm');
            $table->enum('severity', ['light', 'medium', 'serious'])->default('medium')->comment('Mức độ vi phạm');
            $table->dateTime('violation_date')->comment('Thời gian xảy ra');
            $table->unsignedBigInteger('recorded_by')->comment('Người ghi nhận (Chủ nhiệm CLB)');
            $table->enum('status', ['pending', 'processed', 'monitoring'])->default('pending')->comment('Trạng thái xử lý');
            
            // Thông tin kỷ luật (Admin xử lý)
            $table->enum('discipline_type', ['warning', 'reprimand', 'suspension', 'expulsion', 'ban'])->nullable()->comment('Hình thức kỷ luật');
            $table->text('discipline_reason')->nullable()->comment('Lý do xử lý');
            $table->date('discipline_period_start')->nullable()->comment('Thời hạn kỷ luật bắt đầu');
            $table->date('discipline_period_end')->nullable()->comment('Thời hạn kỷ luật kết thúc');
            $table->unsignedBigInteger('processed_by')->nullable()->comment('Admin xử lý');
            $table->timestamp('processed_at')->nullable()->comment('Thời gian xử lý');
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('regulation_id')->references('id')->on('regulations')->onDelete('restrict');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
