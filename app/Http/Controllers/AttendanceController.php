<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\User;

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
            Log::info('Record Details:', [
                'status' => $record->status,
                'in_time' => $record->in_time,
                'out_time' => $record->out_time,
            ]);
        }

        return view('attendance.index', compact('attendanceRecords', 'trainee', 'pageTitle'));
    }

    public function create(Request $request)
    {
        $pageTitle = 'Attendance';
        $attendance = $request->user();

        $attendanceRecords = Attendance::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return view('attendance.create', compact('attendance', 'pageTitle', 'attendanceRecords'));
    }

    public function markAttendance(Request $request)
    {
        $userId = $request->input('student_id');
        $currentDateTime = Carbon::now();
        $currentDate = $currentDateTime->toDateString();
        $imageData = $request->input('image_data');

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $currentDate)
            ->orderBy('in_time', 'desc')
            ->first();

        if ($attendance && $attendance->in_time && $attendance->out_time) {
            return redirect()->back()->with('error', 'Attendance is already completed for today.');
        }

        if (!$attendance) {
            $imagePath = null;

            if ($imageData) {
                $image = str_replace('data:image/png;base64,', '', $imageData);
                $image = str_replace(' ', '+', $image);
                $imageName = 'attendance_images_' . uniqid() . '.png';

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
        try {
            $user = Auth::user();
    
            $attendances = Attendance::where('user_id', $user->id)
                ->with('user')
                ->orderBy('date', 'desc')
                ->get();
    
            $pdf = Pdf::loadView('attendance.pdf', compact('attendances'));
    
            // Stream PDF directly to browser (inline display)
            return $pdf->stream('attendance_report_' . $user->id . '.pdf');
    
            // If you want to force download, use:
            // return $pdf->download('attendance_report_' . $user->id . '.pdf');
    
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
