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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            // Liên kết chuẩn với bảng clubs
            $table->foreignId('club_id')
                  ->constrained('clubs')
                  ->onDelete('cascade');

            $table->string('title');                          // Tiêu đề hoạt động
            $table->text('description')->nullable();          // Mô tả chi tiết
            $table->dateTime('start_time')->nullable();       // Thời gian bắt đầu
            $table->dateTime('end_time')->nullable();         // Thời gian kết thúc
            $table->string('location')->nullable();           // Địa điểm
            $table->integer('max_participants')->nullable();  // Giới hạn người tham gia
            $table->string('status')->default('upcoming');    // upcoming | ongoing | finished

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
