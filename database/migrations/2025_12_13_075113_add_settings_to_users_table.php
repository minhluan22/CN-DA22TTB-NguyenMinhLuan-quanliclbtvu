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
        Schema::table('users', function (Blueprint $table) {
            // Security settings
            $table->boolean('two_factor_enabled')->default(false)->after('avatar');
            $table->json('devices')->nullable()->after('two_factor_enabled');
            
            // Notification settings
            $table->boolean('email_notifications')->default(true)->after('devices');
            $table->boolean('event_notifications')->default(true)->after('email_notifications');
            $table->boolean('club_notifications')->default(true)->after('event_notifications');
            
            // General settings
            $table->string('language')->default('vi')->after('club_notifications');
            $table->boolean('dark_mode')->default(false)->after('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_enabled',
                'devices',
                'email_notifications',
                'event_notifications',
                'club_notifications',
                'language',
                'dark_mode'
            ]);
        });
    }
};
