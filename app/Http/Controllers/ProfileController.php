<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{

    
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();
    $user->fill($request->validated());

    // Handle profile picture upload
   // Handle profile picture upload
if ($request->hasFile('profile_picture')) {
    $profilePic = $request->file('profile_picture');
    $fileName = time() . '.' . $profilePic->getClientOriginalExtension();

    // Store the image in public storage
    $path = $profilePic->storeAs('profile_pictures', $fileName, 'public');

    // Remove old profile picture if it's not the default one
    if ($user->profile_picture && $user->profile_picture !== 'profile_pictures/default.png') {
        $oldPicPath = public_path('storage/' . $user->profile_picture);
        if (file_exists($oldPicPath)) {
            unlink($oldPicPath);
        }
    }

    // Save the new file path in the database
    $user->profile_picture = $path;
} elseif (!$user->profile_picture) {
    // Ensure the default is assigned if missing
    $user->profile_picture = 'profile_pictures/default.png';
}


    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}


}
