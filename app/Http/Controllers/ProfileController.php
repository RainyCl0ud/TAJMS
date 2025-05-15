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
        $originalName = $file->getClientOriginalName();

        \Log::info('Starting profile picture upload', [
            'user_id' => $user->id,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'temp_path_exists' => file_exists($tempPath),
        ]);

        try {
            $fileUrl = $this->drive->uploadFile($tempPath, $mimeType, $originalName);

            if ($fileUrl) {
                $user->profile_picture = str_replace('@https://', 'https://', $fileUrl);
                \Log::info('Profile picture uploaded successfully', [
                    'user_id' => $user->id,
                    'file_url' => $fileUrl,
                ]);
            } else {
                \Log::error('GoogleDriveService returned null URL during upload', [
                    'user_id' => $user->id,
                    'original_name' => $originalName,
                ]);
                return back()->withErrors(['profile_picture' => 'Failed to upload to Google Drive. Please try again later.']);
            }
        } catch (\Exception $e) {
            \Log::error('Exception during Google Drive upload', [
                'user_id' => $user->id,
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['profile_picture' => 'An error occurred while uploading the profile picture.']);
        }
    }

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    \Log::info('User profile updated', ['user_id' => $user->id]);

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}

    
}
