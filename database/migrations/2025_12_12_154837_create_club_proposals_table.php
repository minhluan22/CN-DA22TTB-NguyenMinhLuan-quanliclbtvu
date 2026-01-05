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
        Schema::create('club_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('club_name');
            $table->string('field')->nullable();
            $table->text('objectives')->nullable();
            $table->text('reason')->nullable();
            $table->text('planned_activities')->nullable();
            $table->integer('expected_members')->default(0);
            $table->string('advisor_name')->nullable();
            $table->string('advisor_email')->nullable();
            $table->string('proposer_name');
            $table->string('proposer_email');
            $table->string('proposer_student_code');
            $table->string('member_list_file')->nullable();
            $table->string('activity_plan_file')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_proposals');
    }
};
