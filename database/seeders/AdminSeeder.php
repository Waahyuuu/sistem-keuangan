<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@testing.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('admin'),
                'role' => User::ROLE_ADMIN,
                'kantor_id' => null,
                'departemen_id' => null,
            ]
        );
    }
}
