<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\JournalSeeder;
use Database\Seeders\AttendanceSeeder;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        
        $this->call(CoordinatorSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(AttendanceSeeder::class);
        // $this->call(JournalSeeder::class);
    }
}
