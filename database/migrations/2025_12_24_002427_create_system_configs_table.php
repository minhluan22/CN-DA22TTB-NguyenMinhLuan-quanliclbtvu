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
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->string('config_key')->unique(); // key định danh cấu hình
            $table->string('config_group')->default('general'); // website, email, logo, points, notification
            $table->text('config_value')->nullable(); // giá trị cấu hình (có thể là JSON)
            $table->string('config_type')->default('string'); // string, json, integer, boolean
            $table->text('description')->nullable(); // mô tả cấu hình
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_configs');
    }
};
