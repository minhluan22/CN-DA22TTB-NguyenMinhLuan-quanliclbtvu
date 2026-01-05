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
            $table->enum('activity_type', ['academic', 'arts', 'volunteer', 'other'])->nullable()->after('title');
            $table->text('goal')->nullable()->after('description'); // Mục tiêu
            $table->integer('expected_participants')->nullable()->after('location'); // Số lượng dự kiến
            $table->decimal('expected_budget', 15, 2)->nullable()->after('expected_participants'); // Kinh phí dự kiến
            $table->string('attachment')->nullable()->after('expected_budget'); // File đính kèm
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['activity_type', 'goal', 'expected_participants', 'expected_budget', 'attachment']);
        });
    }
};
