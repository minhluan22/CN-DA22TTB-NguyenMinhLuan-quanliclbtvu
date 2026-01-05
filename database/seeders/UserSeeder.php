<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'          => 'Admin',
            'email'         => 'admin@example.com',
            'password'      => Hash::make('123456'),
            'student_code'  => 'ADMIN001',
            'role_id'       => 1, // Admin = role 1
        ]);
    }
}
