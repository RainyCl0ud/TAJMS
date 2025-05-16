<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Models\Notification;
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
        if (auth()->user()->role === 'coordinator') {
            Notification::where('read', false)->update(['read' => true]);
        }

        $requests = Request::with('trainee')->latest()->get();

        foreach ($requests as $request) {
            // Use Google Drive URL directly if stored
            $request->image_url = $request->image ?: null;
            $request->time_elapsed = Carbon::parse($request->created_at)->diffForHumans();
        }

        return view('coordinator.request', compact('requests'));
    }

    public function store(HttpRequest $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:time_in,time_out',
            'date' => 'required|date',
            'time' => 'required',
            'reason' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

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
                }
            } catch (\Exception $e) {
                \Log::error('Error uploading request image to Google Drive', [
                    'user_id' => Auth::id(),
                    'exception_message' => $e->getMessage(),
                    'file_name' => $originalName,
                ]);
                return redirect()->back()->with('error', 'Failed to upload image. Please try again.');
            }
        }

        Request::create([
            'user_id' => Auth::id(),
            'type' => $validatedData['type'],
            'date' => $validatedData['date'],
            'time' => $validatedData['time'],
            'reason' => $validatedData['reason'],
            'image' => $imageUrl,
        ]);

        return redirect()->back()->with('success', 'Request submitted successfully.');
    }

    public function approve($id)
    {
        $request = Request::findOrFail($id);
        $request->status = 'Approved';
        $request->save();

        return redirect()->back()->with('success', 'Request has been approved!');
    }

    public function reject($id)
    {
        $request = Request::findOrFail($id);
    
        // Delete associated notifications first
        Notification::where('request_id', $request->id)->delete();
        
        // Optional: log or store status before deletion if needed
        $request->status = 'Rejected';
        $request->save();
    
        // Delete the request from database
        $request->delete();
    
        return redirect()->back()->with('success', 'Request has been rejected and removed!');
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
