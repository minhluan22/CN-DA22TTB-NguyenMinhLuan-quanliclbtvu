<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {

            // Sửa members: đang NOT NULL nhưng không có default
            if (Schema::hasColumn('clubs', 'members')) {
                $table->integer('members')->default(0)->change();
            }

            // Sửa activity: đang int NOT NULL, default 0
            if (Schema::hasColumn('clubs', 'activity')) {
                $table->integer('activity')->default(0)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            // Không cần rollback
        });
    }
};
