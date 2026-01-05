<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Admin', 'status' => 1],
            ['id' => 2, 'name' => 'Student', 'status' => 1],
            ['id' => 3, 'name' => 'Guest', 'status' => 1],
        ]);

    }
}
