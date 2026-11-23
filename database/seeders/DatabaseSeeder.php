<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RoomSeeder::class, AdminSeeder::class]);

        if (app()->environment('local')) {
            $this->call(InquirySeeder::class);
        }
    }
}
