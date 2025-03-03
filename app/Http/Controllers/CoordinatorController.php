<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Journal;
use App\Models\Document;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CoordinatorController extends Controller
{
    public function index()
{
    $preUserCount = User::where('role', 'pre_user')->count();
    $traineeCount = User::where('role', 'trainee')->count();

    return view('coordinator.dashboard', compact('preUserCount', 'traineeCount'));
    }  

    public function preUsers()
    {

        $preUsers = User::where('role', 'pre_user')->get();
        return view('coordinator.pre_users', compact('preUsers'));

    }
    
    public function trainees()
    {
        $pageTitle = "Trainees List";
        $trainees = User::with('trainee')->where('role', 'trainee')->get();
        return view('coordinator.trainees', compact('trainees', 'pageTitle'));
    }
    
    public function showUserDocuments($userId)
    {
        $user = User::with('documents')->findOrFail($userId);
        $pageTitle = $user->first_name . ' ' . $user->last_name . "'s Documents";

        return view('coordinator.user-documents', compact('user', 'pageTitle'));
    }

    public function promoteToTrainee(User $user)
    {
    
    $allApproved = $user->documents->every(fn($doc) => $doc->status === 'approved');

    if (!$allApproved) {
        return back()->with('error', 'User cannot be marked as a trainee because not all documents are approved.');
    }

    $user->update(['role' => 'trainee']);

    return back()->with('success', "{$user->name} has been successfully promoted to Trainee.");

    }

    public function records($traineeId)
    {
        $pageTitle = 'Records & Information';
         
        $trainee = User::with('trainee')->findOrFail($traineeId);
        
        $journals = Journal::where('user_id', $traineeId)
        ->orderBy('created_at', 'desc')
        ->get();
    
    $journalCount = $journals->count();  // Get the count of journals
    
    $journals = $journals->map(function ($journal, $index) use ($journalCount) {
        $journal->day = 'Day ' . ($journalCount - $index);  // Reverse the index
        return $journal;
    });
    
    
    
        $attendances = Attendance::where('user_id', $traineeId)
            ->orderBy('date', 'desc')
            ->get();
    
        // Calculate total accumulated hours and minutes
        $totalMinutes = $attendances->reduce(function ($carry, $attendance) {
            if ($attendance->in_time && $attendance->out_time) {
                // Ensure in_time is earlier than out_time to prevent negative results
                if ($attendance->in_time->gt($attendance->out_time)) {
                    return $carry; // Skip invalid time entries
                }
                // Add the positive difference between out_time and in_time
                $carry += $attendance->in_time->diffInMinutes($attendance->out_time);
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
    
        return view('coordinator.trainee-records', compact('trainee', 'journals', 'attendances', 'pageTitle', 'totalHours', 'totalMins', 'remainingHours', 'remainingMins'));
    }

    public function traineeJournalRecords($traineeId)
{
    $pageTitle = 'Journal Records';
    $trainee = User::with('trainee')->findOrFail($traineeId);

    // Get the journals
    $journals = Journal::where('user_id', $traineeId)
        ->orderBy('created_at', 'desc')
        ->get();

    // Get the count of journals to calculate the day correctly
    $journalsCount = $journals->count();

    // Map the journals and assign day number
    $journals = $journals->map(function ($journal, $index) use ($journalsCount) {
        $journal->day = 'Day ' . ($journalsCount - $index);  
        return $journal;
    });

    return view('coordinator.trainee-journal-records', compact('journals', 'pageTitle', 'trainee'));
    }

    public function attendanceAll()
    {
        session(['previous_url' => URL::full()]);


        $pageTitle = 'Attendance Today'; 
        $today = now()->toDateString();
        
        $trainees = User::with('trainee')->where('role', 'trainee')->get();
        $attendancesToday = Attendance::with('user')
            ->where('date', $today)
            ->get()
            ->keyBy('user_id'); // Key by user_id for easier access
    
        foreach ($trainees as $trainee) {

            // Check if the trainee has a record for today
            $attendance = $attendancesToday->get($trainee->id);
    
            // If there's no attendance, set their status to "Not working"
            $trainee->attendance_status = $attendance ? 'Present' : 'Not working';
        }
    
        return view('coordinator.trainee-attendance-all-records', compact('trainees', 'pageTitle'));
    }
    

    public function traineeJournalEntry($journalId)
    {
        // Fetch the journal entry
        $journal = Journal::findOrFail($journalId);
        
        // Get the trainee (user who owns the journal)
        $trainee = $journal->user;
    
        // Fetch all trainee journals in ascending order (oldest first)
        $journals = Journal::where('user_id', $trainee->id)
            ->orderBy('created_at', 'asc')
            ->get();
    
        // Recalculate the "Day X" number
        $day = $journals->search(function ($item) use ($journal) {
            return $item->id === $journal->id;
        }) + 1; // Since array index starts at 0
    
        // Generate page title dynamically
        $pageTitle =  $trainee->first_name . ' ' .$trainee->last_name . ' / Journal / Day ' . $day;
    
        return view('coordinator.trainee-journal-entry', compact('journal', 'pageTitle', 'trainee', 'day'));
    }    


    public function addRenderedHours(Request $request, $traineeId)
    {
        // Find the trainee by their ID
        $trainee = User::findOrFail($traineeId);
    
        // Check if the trainee has any attendance records
        $attendance = $trainee->attendance()->first();
    
        $additionalMinutes = ($request->hours * 60) + $request->minutes;

        if ($attendance) {
            // Calculate the current accumulated time if in_time and out_time exist
            $currentMinutes = 0;
            if ($attendance->in_time && $attendance->out_time) {
                $currentMinutes = $attendance->in_time->diffInMinutes($attendance->out_time);
            }
    
            // Add the manually provided hours and minutes
            $totalMinutes = $currentMinutes + $additionalMinutes;
    
            // Add the total minutes to the in_time to calculate the new out_time
            if ($attendance->in_time) {
                $newOutTime = $attendance->in_time->addMinutes($totalMinutes);
                $attendance->out_time = $newOutTime;
                $attendance->save();
            } else {
                return redirect()->back()->with('error', 'No in-time recorded for this trainee.');
            }
        } else {
            // If no attendance record exists, create a new one
            $attendance = new Attendance();
            $attendance->user_id = $traineeId;
            $attendance->date = now()->toDateString(); // Set today's date
            $attendance->in_time = now(); // Set in_time to current time (or another value as needed)
            $attendance->out_time = now()->addMinutes($additionalMinutes); // Calculate out_time
            $attendance->save();
        }
    
        return redirect()->back()->with('success', 'Rendered hours added successfully!');
    }
    
    
}


