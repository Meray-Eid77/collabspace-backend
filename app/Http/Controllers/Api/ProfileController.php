<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json(['success' => true, 'data' => [
            'user' => $user,
            'stats' => [
                'completed_tasks' => $user->tasks()->where('status', 'completed')->count(),
                'total_tasks' => $user->tasks()->count(),
            ],
            'tasks' => $user->tasks()->with('project:id,name')->latest()->get(),
            'files' => $user->files()->latest()->get(),
        ]]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $request->user()->id],
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

        return response()->json(['success' => true, 'message' => 'Profile updated', 'data' => $request->user()->fresh()]);
    }
}
