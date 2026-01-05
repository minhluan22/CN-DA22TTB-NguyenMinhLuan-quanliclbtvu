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
        Schema::table('notifications', function (Blueprint $table) {
            // Loại thông báo: system, regulation, administrative
            $table->enum('type', ['system', 'regulation', 'administrative'])->default('system')->after('body');
            
            // Đối tượng nhận: all, students, chairmen, clubs
            $table->enum('target_type', ['all', 'students', 'chairmen', 'clubs'])->default('all')->after('type');
            
            // ID các CLB cụ thể (nếu target_type = 'clubs'), lưu dạng JSON
            $table->json('target_ids')->nullable()->after('target_type');
            
            // Thời gian gửi thực tế
            $table->timestamp('sent_at')->nullable()->after('target_ids');
            
            // Thời gian lên lịch gửi (nếu gửi sau)
            $table->timestamp('scheduled_at')->nullable()->after('sent_at');
            
            // Trạng thái: draft, scheduled, sent
            $table->enum('status', ['draft', 'scheduled', 'sent'])->default('draft')->after('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'target_type',
                'target_ids',
                'sent_at',
                'scheduled_at',
                'status'
            ]);
        });
    }
};
