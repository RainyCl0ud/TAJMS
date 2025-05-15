<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\GoogleDriveService;

class ProfileController extends Controller
{
    protected $drive;

    public function __construct(GoogleDriveService $drive)
    {
        $this->drive = $drive;
    }

    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $user->fill($request->validated());
    
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $mimeType = $file->getMimeType();
            $tempPath = $file->getRealPath();
            $originalName = $file->getClientOriginalName(); // ✅ Get actual filename
    
            $fileUrl = $this->drive->uploadFile($tempPath, $mimeType, $originalName);
    
            if ($fileUrl) {
                $user->profile_picture = str_replace('@https://', 'https://', $fileUrl);
            } else {
                return back()->withErrors(['profile_picture' => 'Failed to upload to Google Drive.']);
            }
        }
    
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    
        $user->save();
    
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    
}
