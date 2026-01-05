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
        Schema::table('events', function (Blueprint $table) {
            $table->text('violation_notes')->nullable()->after('approval_status');
            $table->unsignedBigInteger('created_by')->nullable()->after('club_id');
            $table->timestamp('deleted_at')->nullable()->after('updated_at');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
            
            // Thêm 'disabled' vào enum status
            DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('upcoming', 'ongoing', 'finished', 'cancelled', 'disabled') DEFAULT 'upcoming'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['violation_notes', 'created_by', 'deleted_at', 'deleted_by']);
            DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('upcoming', 'ongoing', 'finished', 'cancelled') DEFAULT 'upcoming'");
        });
    }
};
