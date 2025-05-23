<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TraineeController extends Controller
{
    public function index()
    {
        $pageTitle = 'Dashboard';

        $attendances = Attendance::where('user_id', Auth::id())
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function ($attendance, $index) {
            $attendance->day = 'Day' . ($index + 1);
            return $attendance;
        })->reverse();
        $journals = Journal::where('user_id', Auth::id())
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function ($journal, $index) {
            $journal->day = 'Day ' . ($index + 1); 
            return $journal;
        })->reverse();

        // Calculate total accumulated hours and minutes
        $totalMinutes = $attendances->reduce(function ($carry, $attendance) {
            if ($attendance->in_time && $attendance->out_time) {
                // Ensure in_time is earlier than out_time to prevent negative results
                if ($attendance->in_time->gt($attendance->out_time)) {
                    return $carry; // Skip invalid time entries
                }
                // Calculate minutes for this attendance
                $minutes = $attendance->in_time->diffInMinutes($attendance->out_time);
                
                // Deduct 1 hour break if work duration is more than 5 hours
                if ($minutes > 300) { // 5 hours * 60 minutes
                    $minutes -= 60; // Deduct 60 minutes for break
                }
                
                $carry += $minutes;
            }
            return $carry;
        }, 0);

        // Convert total accumulated minutes to hours and minutes
        $totalHours = floor($totalMinutes / 60);
        $totalMins = $totalMinutes % 60;

        // Total required hours (438 hours converted to minutes)
        $requiredHours = 438 * 60; // 438 hours in minutes
        $remainingMinutes = max(0, $requiredHours - $totalMinutes);

        // Convert remaining minutes to hours and minutes
        $remainingHours = floor($remainingMinutes / 60);
        $remainingMins = $remainingMinutes % 60;
        
        return view('trainee.dashboard', compact(
            'journals', 
            'attendances',
            'pageTitle',
            'totalHours',
            'totalMins',
            'remainingHours',
            'remainingMins'
        ));
    }
}
