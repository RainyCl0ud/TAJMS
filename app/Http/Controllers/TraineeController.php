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
        
        return view('trainee.dashboard', compact('journals', 'attendances','pageTitle'));
    }
}
