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
        Schema::create('club_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('position', ['chairman', 'vice_chairman', 'member'])->default('member'); // Chức vụ
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended', 'left'])->default('pending'); // Trạng thái
            $table->date('joined_date')->nullable(); // Ngày tham gia
            $table->text('notes')->nullable(); // Ghi chú
            
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['club_id', 'user_id']); // Mỗi user chỉ là thành viên 1 lần trong CLB
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_members');
    }
};
