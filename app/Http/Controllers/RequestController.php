<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Absent;
use App\Models\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request as HttpRequest;

class RequestController extends Controller
{
    public function index() 
    {
        // Mark all notifications as read when coordinator views requests
        if (auth()->user()->role === 'coordinator') {
            \App\Models\Notification::whereHas('request', function ($query) {
                $query->where('coordinator_id', auth()->id());
            })->update(['read' => true]);
        }

        $requests = Request::with('trainee')->latest()->get();
    
        foreach ($requests as $request) {
            if ($request->image) {
                $request->image_url = asset('storage/' . $request->image);
            }
    
            // Calculate time difference (hours/minutes)
            $request->time_elapsed = Carbon::parse($request->created_at)->diffForHumans();
        }
    
    
        return view('coordinator.request', compact('requests'));
    }








    public function delete($id)
{
    $request = Request::findOrFail($id);
    $request->delete();

    return redirect()->back()->with('success', 'Request has been deleted!');
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

        // Handle image upload
        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('uploads', 'public');
        }

        // Assign the currently logged-in user
        $validatedData['user_id'] = Auth::id();

        Request::create($validatedData);

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
        $request->status = 'Rejected';
        $request->save();
    
        return redirect()->back()->with('success', 'Request has been rejected!');
    }


}
