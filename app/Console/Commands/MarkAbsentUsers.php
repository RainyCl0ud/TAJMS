<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Console\Command;

class MarkAbsentUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-absent-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark absent users for today.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDate = Carbon::now()->toDateString();
        
        // Get all trainees
        $trainees = User::all();
    
        foreach ($trainees as $trainee) {
            // Check if they have attendance for today
            $hasAttendance = Attendance::where('user_id', $trainee->id)
                ->where('date', $currentDate)
                ->exists();
    
            if (!$hasAttendance) {
                // Mark as absent
                Attendance::create([
                    'user_id' => $trainee->id,
                    'date' => $currentDate,
                    'status' => 'Absent',
                    'in_time' => null,
                    'out_time' => null,
                ]);
            }
        }
    
        $this->info('Absent users have been marked successfully.');
    }
}
