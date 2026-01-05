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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_code')) {
                $table->string('student_code')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->default(1)->after('student_code');
            }

            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

        public function down()
        {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('student_code');
            });
        }

};
