<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('field')->nullable();      // Lĩnh vực
            $table->string('chairman')->nullable();   // Chủ nhiệm
            $table->integer('members')->default(0);   // Số thành viên
            $table->integer('activity')->default(0);  // Hoạt động
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
