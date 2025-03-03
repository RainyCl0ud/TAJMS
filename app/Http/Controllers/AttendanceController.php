<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{   
    public function index(Request $request)
    {
        $traineeId = $request->get('traineeId');
        $pageTitle = 'Attendance Records';
        
       
        $trainee = User::findOrFail($traineeId);
  
        $attendanceRecords = Attendance::where('user_id', $traineeId)
            ->orderBy('date', 'desc')
            ->get();
        
        foreach ($attendanceRecords as $record) {
            logger()->info('Record Details:', [
                'status' => $record->status, 
                'in_time' => $record->in_time, 
                'out_time' => $record->out_time,
            ]);
        }
        
        // Return the view with attendance records and trainee details
        return view('attendance.index', compact('attendanceRecords', 'trainee', 'pageTitle'));
    }

    public function create(Request $request)
{
    $pageTitle = 'Attendance';
    $attendance = $request->user();

    // Fetch the attendance records for the logged-in user
    $attendanceRecords = Attendance::where('user_id', Auth::user()->id)
        ->orderBy('date', 'desc')
        ->get();

    return view('attendance.create', compact('attendance', 'pageTitle', 'attendanceRecords'));
}

public function markAttendance(Request $request)
{
    $userId = $request->input('student_id');
    $currentDateTime = Carbon::now(); // Current timestamp
    $currentDate = $currentDateTime->toDateString(); // Current date only
    $imageData = $request->input('image_data');
    
    // Fetch the latest attendance record for the user on the current date
    $attendance = Attendance::where('user_id', $userId)
        ->where('date', $currentDate)
        ->orderBy('in_time', 'desc')
        ->first();
 
    // Check if attendance is already completed
    if ($attendance && $attendance->in_time && $attendance->out_time) {
        return redirect()->back()->with('error', 'Attendance is already completed for today.');
    }

    // Handle creating or updating attendance
    if (!$attendance) {
        // First interaction: Create new attendance record
        $imagePath = null;

        if ($imageData) {
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'attendance_images' . uniqid() . '.png';

            // Save the image
            Storage::disk('attendance_images')->put($imageName, base64_decode($image));
            $imagePath = 'storage/' . $imageName;
        }

        Attendance::create([
            'user_id' => $userId,
            'date' => $currentDate,
            'in_time' => $currentDateTime,
            'out_time' => null,
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Time In recorded successfully!');
    }

    if (!$attendance->out_time) {
        $attendance->update(['out_time' => $currentDateTime]);
        return redirect()->back()->with('success', 'Time Out recorded successfully!');
    }

    return redirect()->back()->with('error', 'An unexpected error occurred.');
}

    public function getAttendanceRecords(Request $request)
    {
       
        $userId = $request->user()->id;
        $attendances = Attendance::where('user_id', $userId)->get();
    
        $attendances = $attendances->map(function ($attendance) {
            $currentTime = Carbon::now();
            $status = '';
    
            if ($attendance->in_time && $attendance->out_time) {
                $status = 'Completed';
            } elseif ($attendance->in_time && !$attendance->out_time) {
                $status = 'Incomplete';
            } elseif ($attendance->date < $currentTime->subDay()->toDateString()) {
                $status = 'Absent';
            }
    
            $attendance->status = $status;
            return $attendance;
        });
    
        return view('attendance.index', compact('attendances'));
    } 

    public function previewPdf()
    {
        // Get the currently authenticated user
        $user = Auth::user();
    
        // Fetch only the attendance records for the logged-in user
        $attendances = Attendance::where('user_id', $user->id)
            ->with('user')
            ->orderBy('date', 'desc')
            ->get();
    
        $pdf = Pdf::loadView('attendance.pdf', compact('attendances'));
    
        // Store PDF in storage/public and return its URL
        $pdfPath = 'attendance_records_' . $user->id . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());
    
        return response()->json([
            'url' => Storage::url($pdfPath)  // This returns the URL of the user's PDF
        ]);
    }
    

}
