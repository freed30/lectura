<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@lectura.local')],
            [
                'name' => 'Admin Lectura',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'LecturaAdmin2026!')),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
