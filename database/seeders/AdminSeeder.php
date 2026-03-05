<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Ensures a default admin user exists. firstOrCreate avoids duplicate key if seeder runs multiple times.
 * Password is hashed by User model's 'password' => 'hashed' cast.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'System Admin',
                'password' => 'password',
                'role'     => User::ROLE_ADMIN,
            ]
        );
    }
}
