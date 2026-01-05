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
            // Nguồn thông báo: admin (từ Admin hệ thống) hoặc club (từ Chủ nhiệm CLB)
            $table->enum('notification_source', ['admin', 'club'])->default('admin')->after('sender_id');
            
            // CLB ID (nếu notification_source = 'club')
            $table->unsignedBigInteger('club_id')->nullable()->after('notification_source');
            
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn(['notification_source', 'club_id']);
        });
    }
};
