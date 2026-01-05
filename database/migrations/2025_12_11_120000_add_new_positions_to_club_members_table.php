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
        // Cập nhật enum position để thêm các chức vụ mới
        DB::statement("ALTER TABLE club_members MODIFY COLUMN position ENUM(
            'chairman',
            'vice_chairman',
            'secretary',
            'head_expertise',
            'head_media',
            'head_events',
            'treasurer',
            'member'
        ) DEFAULT 'member'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert về enum cũ
        DB::statement("ALTER TABLE club_members MODIFY COLUMN position ENUM(
            'chairman',
            'vice_chairman',
            'member'
        ) DEFAULT 'member'");
    }
};

