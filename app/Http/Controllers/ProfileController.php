<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $tab = $request->get('tab', 'overview');

        $stats = [
            'completed' => $user->tasks()->where('status', 'completed')->count(),
            'total' => $user->tasks()->count(),
        ];

        $tasks = $user->tasks()->with('project')->latest()->get();
        $files = $user->files()->latest()->get();

        return view('profile.show', compact('user', 'tab', 'stats', 'tasks', 'files'));
    }

    public function settings(Request $request)
    {
        return view('profile.settings', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'availability' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'years_of_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'current_team' => ['nullable', 'string', 'max:150'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $request->user()->update($data);

        return redirect('/profile/settings')->with('success', 'Profile updated');
    }
}
