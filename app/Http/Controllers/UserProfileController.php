<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * Display the user's profile with their reviews.
     */
    public function show()
    {
        $user = auth()->user();

        $reviews = $user->reviews()
            ->with('teacher')
            ->withCount('votes')
            ->orderByDesc('created_at')
            ->paginate(10);

        $helpfulVotes = $user->helpfulVotesReceived();

        return view('profile.show', compact('user', 'reviews', 'helpfulVotes'));
    }

    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    /**
     * Update the user's profile.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }
}
