<?php

namespace App\Http\Controllers;

use Illuminate\Http\{RedirectResponse, Request};
use Inertia\{Inertia, Response};

class UserProfileController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('Profile/Show', [
            'userProfile' => $request->user()->userProfile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'phone'    => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
        ]);

        $request->user()->userProfile->update($request->only('phone', 'position'));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
