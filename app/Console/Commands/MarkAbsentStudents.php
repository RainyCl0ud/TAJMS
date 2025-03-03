<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class MarkAbsentStudents extends Command
{
    protected $signature = 'attendance:mark-absent';
    protected $description = 'Mark students as absent if they did not log attendance for the day';

    public function handle()
    {
        $today = Carbon::today()->toDateString(); // Get today's date

        // Get all students (or filter by role if necessary)
        $students = User::where('role', 'trainee')->get(); // Adjust role if needed

        foreach ($students as $student) {
            // Check if an attendance record exists for today
            $hasAttendance = Attendance::where('user_id', $student->id)
                ->where('date', $today)
                ->exists();

            // If no attendance record exists, mark as absent
            if (!$hasAttendance) {
                Attendance::create([
                    'user_id' => $student->id,
                    'date' => $today,
                    'in_time' => null,
                    'out_time' => null,
                    'image' => null,
                ]);
            }
        }

        $this->info('Absent students marked successfully.');
    }
}
