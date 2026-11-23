<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@bluehouse.test');
        $password = env('ADMIN_PASSWORD', 'password');

        User::updateOrCreate(['email' => $email], [
            'name' => 'Admin Blue House',
            'password' => Hash::make($password),
        ]);

        if ($password === 'password') {
            $this->command?->warn("Admin dibuat: {$email} / password  — SEGERA ganti (set ADMIN_PASSWORD di .env atau jalankan php artisan kos:admin).");
        }
    }
}
