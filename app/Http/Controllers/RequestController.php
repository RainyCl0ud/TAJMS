<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Models\Notification;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request as HttpRequest;
use App\Services\GoogleDriveService;

class RequestController extends Controller
{
    protected $drive;

    public function __construct(GoogleDriveService $drive)
    {
        $this->drive = $drive;
    }

    public function index() 
    {
        $pageTitle = 'Request';
        if (auth()->user()->role === 'coordinator') {
            Notification::where('read', false)->update(['read' => true]);
        }

        $status = request()->query('status', 'recent');
        $query = Request::with('user')->latest();

        if ($status === 'recent') {
            // Show only pending and approved requests from the last 7 days
            $query->where(function($q) {
                $q->where('status', 'pending')
                  ->orWhere('status', 'approved');
            })->where('created_at', '>=', now()->subDays(7));
        } elseif ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->get();

        foreach ($requests as $request) {
            // Use Google Drive URL directly if stored
            $request->image_url = $request->image ?: null;
            $request->time_elapsed = Carbon::parse($request->created_at)->diffForHumans();
        }

        return view('coordinator.request', compact('requests', 'pageTitle', 'status'));
    }

    public function store(HttpRequest $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string|in:time_in,time_out,manual_attendance',
            'date' => 'required|date|before_or_equal:today',
            'time' => 'required',
            'time_out' => $request->input('type') === 'manual_attendance' ? 'required|after:time' : 'nullable',
            'reason' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // For manual attendance, verify there's no existing attendance record
        if ($validatedData['type'] === 'manual_attendance') {
            $existingAttendance = Attendance::where('user_id', Auth::id())
                ->whereDate('date', $validatedData['date'])
                ->first();

            if ($existingAttendance) {
                return redirect()->back()->with('error', 'Attendance record already exists for this date.');
            }
        } else {
            // Check if there's already an attendance record for this date
            $attendance = Attendance::where('user_id', Auth::id())
                ->whereDate('date', $validatedData['date'])
                ->first();

            // For time_in requests, verify there's no existing time_in
            if ($validatedData['type'] === 'time_in') {
                if (!$attendance) {
                    return redirect()->back()->with('error', 'No attendance record found for this date. Please take attendance with photo capture first.');
                }
                if ($attendance->in_time) {
                    return redirect()->back()->with('error', 'Time in already exists for this date.');
                }
            }

            // For time_out requests, verify there's an attendance record with time_in
            if ($validatedData['type'] === 'time_out') {
                if (!$attendance) {
                    return redirect()->back()->with('error', 'No attendance record found for this date. Please take attendance with photo capture first.');
                }
                if (!$attendance->in_time) {
                    return redirect()->back()->with('error', 'No time in record found for this date. Please record your time in first.');
                }
                if ($attendance->out_time) {
                    return redirect()->back()->with('error', 'Time out already exists for this date.');
                }
            }
        }

        $imageUrl = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $mimeType = $image->getMimeType();
            $tempPath = $image->getRealPath();
            $originalName = $image->getClientOriginalName();

            try {
                $uploadedUrl = $this->drive->uploadFile($tempPath, $mimeType, $originalName);
                if ($uploadedUrl) {
                    $imageUrl = str_replace('@https://', 'https://', $uploadedUrl);
                } else {
                    \Log::error('Failed to get upload URL from Google Drive', [
                        'user_id' => Auth::id(),
                        'file_name' => $originalName,
                    ]);
                    return redirect()->back()
                        ->with('error', 'Unable to upload attachment. Please try again later.')
                        ->withInput();
                }
            } catch (\Exception $e) {
                \Log::error('Error uploading request image to Google Drive', [
                    'user_id' => Auth::id(),
                    'exception_message' => $e->getMessage(),
                    'file_name' => $originalName,
                    'stack_trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()
                    ->with('error', 'Failed to upload image. Please try again later.')
                    ->withInput();
            }
        }

        try {
            Request::create([
                'user_id' => Auth::id(),
                'type' => $validatedData['type'],
                'date' => $validatedData['date'],
                'time' => $validatedData['time'],
                'time_out' => $validatedData['type'] === 'manual_attendance' ? $validatedData['time_out'] : null,
                'reason' => $validatedData['reason'],
                'image' => $imageUrl,
                'status' => 'pending'
            ]);

            return redirect()->back()->with('success', 'Request submitted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating request', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $validatedData
            ]);
            
            // Check if it's a database error
            if (str_contains($e->getMessage(), 'SQLSTATE')) {
                return redirect()->back()
                    ->with('error', 'Database error occurred. Please try again or contact support.')
                    ->withInput();
            }
            
            // Check if it's a Google Drive error
            if (str_contains($e->getMessage(), 'Google')) {
                return redirect()->back()
                    ->with('error', 'Failed to upload image. Please try again or contact support.')
                    ->withInput();
            }
            
            return redirect()->back()
                ->with('error', 'Failed to submit request: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function approve($id)
    {
        $request = Request::findOrFail($id);
        $requestDate = Carbon::parse($request->date);
        
        if ($request->type === 'manual_attendance') {
            $timeIn = Carbon::parse($request->time);
            $timeOut = Carbon::parse($request->time_out);
            
            // Create attendance record with both time in and time out
            $attendance = Attendance::create([
                'user_id' => $request->user_id,
                'date' => $requestDate->toDateString(),
                'in_time' => Carbon::create(
                    $requestDate->year,
                    $requestDate->month,
                    $requestDate->day,
                    $timeIn->hour,
                    $timeIn->minute,
                    0
                ),
                'out_time' => Carbon::create(
                    $requestDate->year,
                    $requestDate->month,
                    $requestDate->day,
                    $timeOut->hour,
                    $timeOut->minute,
                    0
                )
            ]);
            
            // Calculate rendered hours
            $renderedMinutes = $timeIn->diffInMinutes($timeOut);
            
            // Validate reasonable work hours (max 16 hours)
            if ($renderedMinutes > 960) { // 16 hours * 60 minutes
                return redirect()->back()->with('error', 'Invalid time out: Duration exceeds 16 hours.');
            }

            // Deduct 1 hour break time if work duration is more than 5 hours
            if ($renderedMinutes > 300) { // 5 hours * 60 minutes
                $renderedMinutes -= 60; // Deduct 60 minutes (1 hour) for break
            }
            
            $attendance->rendered_hours = round($renderedMinutes / 60, 2);
            $attendance->save();
        } else {
            $requestTime = Carbon::parse($request->time);
            
            // Combine date and time
            $requestDateTime = Carbon::create(
                $requestDate->year,
                $requestDate->month,
                $requestDate->day,
                $requestTime->hour,
                $requestTime->minute,
                0
            );

            // Find or create attendance record for the date
            $attendance = Attendance::firstOrNew([
                'user_id' => $request->user_id,
                'date' => $requestDate->toDateString()
            ]);

            // For time_out requests, verify there's a time_in record
            if ($request->type === 'time_out') {
                if (!$attendance->exists || !$attendance->in_time) {
                    return redirect()->back()->with('error', 'Cannot approve time out request without a time in record.');
                }
            }

            // Update the appropriate time based on request type
            if ($request->type === 'time_in') {
                $attendance->in_time = $requestDateTime;
                // If out_time exists and is before in_time, clear it
                if ($attendance->out_time && $attendance->out_time->lt($requestDateTime)) {
                    $attendance->out_time = null;
                }
            } else { // time_out
                $attendance->out_time = $requestDateTime;
                
                // Calculate rendered hours with validation
                if ($attendance->in_time) {
                    if ($requestDateTime->lt($attendance->in_time)) {
                        return redirect()->back()->with('error', 'Time out cannot be earlier than time in.');
                    }
                    
                    $renderedMinutes = $attendance->in_time->diffInMinutes($requestDateTime);
                    
                    // Validate reasonable work hours (max 16 hours)
                    if ($renderedMinutes > 960) { // 16 hours * 60 minutes
                        return redirect()->back()->with('error', 'Invalid time out: Duration exceeds 16 hours.');
                    }

                    // Deduct 1 hour break time if work duration is more than 5 hours
                    if ($renderedMinutes > 300) { // 5 hours * 60 minutes
                        $renderedMinutes -= 60; // Deduct 60 minutes (1 hour) for break
                    }
                    
                    $attendance->rendered_hours = round($renderedMinutes / 60, 2);
                }
            }

            $attendance->save();
        }

        // Update request status
        $request->status = 'approved';
        $request->save();

        return redirect()->back()->with('success', 'Request approved and attendance updated successfully!');
    }

    public function reject($id)
    {
        $request = Request::findOrFail($id);
        
        // Update request status to rejected
        $request->status = 'rejected';
        $request->save();
    
        return redirect()->back()->with('success', 'Request has been rejected!');
    }
    
    public function delete($id)
    {
        $request = Request::findOrFail($id);
        
        // Delete associated notifications first
        Notification::where('request_id', $request->id)->delete();
        
        $request->delete();

        return redirect()->back()->with('success', 'Request has been deleted!');
    }
}
