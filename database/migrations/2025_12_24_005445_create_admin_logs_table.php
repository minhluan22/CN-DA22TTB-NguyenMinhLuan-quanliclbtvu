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
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('action'); // create, update, delete, approve, etc.
            $table->string('model_type'); // User, Club, Activity, SystemConfig, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description')->nullable(); // Mô tả chi tiết hành động
            $table->json('old_data')->nullable(); // Dữ liệu cũ (trước khi thay đổi)
            $table->json('new_data')->nullable(); // Dữ liệu mới (sau khi thay đổi)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['admin_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
