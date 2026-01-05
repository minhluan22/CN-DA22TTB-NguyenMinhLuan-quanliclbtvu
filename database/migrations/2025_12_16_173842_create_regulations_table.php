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
        Schema::create('regulations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Mã nội quy');
            $table->string('title')->comment('Tiêu đề nội quy');
            $table->text('content')->comment('Nội dung chi tiết');
            $table->enum('scope', ['all_clubs', 'specific_club'])->default('all_clubs')->comment('Phạm vi áp dụng');
            $table->unsignedBigInteger('club_id')->nullable()->comment('CLB cụ thể (nếu scope = specific_club)');
            $table->enum('severity', ['light', 'medium', 'serious'])->default('medium')->comment('Mức độ: Nhẹ / Trung bình / Nghiêm trọng');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Trạng thái: Đang áp dụng / Ngừng áp dụng');
            $table->date('issued_date')->comment('Ngày ban hành');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Người tạo');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Người cập nhật');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulations');
    }
};
