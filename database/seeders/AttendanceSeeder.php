<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Get some random users from the users table
        $users = User::all();

        foreach ($users as $user) {
            // Generate a random attendance record for each user
            $date = Carbon::now()->subDays(rand(0, 30)); // Random date within the last 30 days
            $inTime = $date->copy()->setTime(rand(8, 10), rand(0, 59)); // Random in-time between 8:00 and 10:00 AM
            $outTime = $inTime->copy()->addMinutes(rand(60, 240)); // Random out-time between 1 to 4 hours after in-time
            
            // Insert a new attendance record
            Attendance::create([
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'in_time' => $inTime,
                'out_time' => $outTime,
                'image' => null, // You can add a sample image URL here if needed
            ]);
        }
    }
}
