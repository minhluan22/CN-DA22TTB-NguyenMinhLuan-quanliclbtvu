<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('club_members', function (Blueprint $table) {
            $table->integer('join_count')->default(1)->after('joined_date'); // Số lần tham gia CLB
        });
        
        // Cập nhật join_count cho các bản ghi hiện có
        DB::statement('UPDATE club_members SET join_count = 1 WHERE join_count IS NULL OR join_count = 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_members', function (Blueprint $table) {
            $table->dropColumn('join_count');
        });
    }
};
