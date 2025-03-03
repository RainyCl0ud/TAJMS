<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id'); // Get all user IDs

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed the users table first.');
            return;
        }

        foreach (range(1, 10) as $index) {
            Journal::create([
                'user_id' => $users->random(),
                'content' => "This is a sample journal entry #$index.",
                'image' => 'images/sample' . $index . '.jpg', // Assume images are stored in 'public/images/'
            ]);
        }
    }
}
