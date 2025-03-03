<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        $role = Auth::user()->role; // Assuming you have a role attribute
    
        // Fetch messages based on the user's role
        if ($role == 'trainee') {
            // Fetch only messages the trainee has received
            $messages = Message::where('receiver_id', $userId)
                                ->orderBy('created_at', 'asc')
                                ->get();
        } else {
            // For coordinator, fetch all messages where they are either the sender or receiver
            $messages = Message::where(function($query) use ($userId) {
                    $query->where('sender_id', $userId)
                          ->orWhere('receiver_id', $userId);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }
        
        
        return view('messages.index', compact('messages', 'role'));
    }
    
    

    public function send(Request $request)
    {
        // Validate the input data
        $request->validate([
            'receiver_id' => 'required|exists:users,id', // Ensure receiver exists
            'message' => 'required|string', // Ensure the message is not empty
            'attachment' => 'nullable|file|mimes:jpg,png,pdf,docx|max:2048', // Ensure valid attachment types
        ]);

        // Create a new message instance
        $message = new Message();
        $message->sender_id = Auth::user()->id; // Set the sender to the logged-in user
        $message->receiver_id = $request->receiver_id; // Get the receiver from the form input
        $message->message = $request->message; // Set the message text
        
        // Handle file upload if there is an attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            // Store the file in the 'public/messages' directory
            $path = $file->store('messages', 'public');
            $message->attachment = $path; // Save the file path in the database
        }
    
        // Save the message to the database
        $message->save();
    
        // Redirect back to the messages page with success message
        return redirect()->route('messages.index')->with('success', 'Message sent successfully!');
    }
}