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
        Schema::create('support_requests', function (Blueprint $table) {
            $table->id();
            
            // Thông tin người gửi
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('cascade');
            $table->enum('sender_type', ['guest', 'student', 'chairman'])->default('student');
            
            // Thông tin liên hệ (cho guest)
            $table->string('name')->nullable(); // Tên người gửi (guest)
            $table->string('email')->nullable(); // Email (guest)
            $table->string('student_code')->nullable(); // MSSV (tự động lấy từ user)
            
            // Nội dung yêu cầu
            $table->string('subject'); // Tiêu đề
            $table->text('content'); // Nội dung
            
            // Trạng thái xử lý
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            
            // Phản hồi từ admin
            $table->text('admin_response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('responded_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('club_id');
            $table->index('sender_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_requests');
    }
};
