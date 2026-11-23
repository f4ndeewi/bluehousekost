<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'kos:admin {email} {password} {--name=Admin}';

    protected $description = 'Buat atau perbarui akun admin Blue House Kos';

    public function handle(): int
    {
        if (strlen($this->argument('password')) < 8) {
            $this->error('Kata sandi minimal 8 karakter.');

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $this->argument('email')],
            ['name' => $this->option('name'), 'password' => Hash::make($this->argument('password'))]
        );

        $this->info('Admin siap: ' . $this->argument('email'));

        return self::SUCCESS;
    }
}
