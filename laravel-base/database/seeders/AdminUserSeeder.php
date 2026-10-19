<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@hoangthuocmedical.com');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Hoang Thuoc Medical'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'HoangThuoc@2026')),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
