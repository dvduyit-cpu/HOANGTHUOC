<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || User::where('email', $email)->exists()) {
            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || ! is_string($password) || mb_strlen($password) < 10) {
            throw new \RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD (at least 10 characters) before seeding the admin.');
        }

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Hoang Thuoc Medical'),
                'password' => $password,
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
