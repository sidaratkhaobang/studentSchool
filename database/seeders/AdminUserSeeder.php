<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email'     => 'admin@studentschool.ac.th',
                'password'  => 'Admin1234!',
                'role'      => 'admin',
                'is_active' => true,
            ]
        );
    }
}
