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
            // Change enum to include 'active', 'pending', 'locked'
            // Note: MySQL requires modifying the column definition
            DB::statement("ALTER TABLE clubs MODIFY COLUMN status ENUM('active', 'pending', 'locked') DEFAULT 'active'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('clubs', function (Blueprint $table) {
            DB::statement("ALTER TABLE clubs MODIFY COLUMN status ENUM('active','pending','archived') DEFAULT 'pending'");
        });
    }
};
