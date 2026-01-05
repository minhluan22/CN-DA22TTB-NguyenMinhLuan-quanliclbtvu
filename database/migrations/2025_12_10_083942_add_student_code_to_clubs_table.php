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
            // Kiểm tra cột đã tồn tại hay chưa trước khi thêm
            if (!Schema::hasColumn('clubs', 'student_code')) {
                $table->string('student_code')->nullable();  // Thêm cột MSSV vào bảng clubs
            }
        });
    }

public function down()
{
    Schema::table('clubs', function (Blueprint $table) {
        $table->dropColumn('student_code');
    });
}

};
