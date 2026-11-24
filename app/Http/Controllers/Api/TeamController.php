<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Project $project)
    {
        return response()->json([
            'success' => true,
            'data' => $project->teams()->with('members:id,name,job_title')->get(),
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);
        $team = $project->teams()->create($data);

        return response()->json(['success' => true, 'message' => 'Team created', 'data' => $team], 201);
    }

    public function addMember(Request $request, Team $team)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['nullable', 'string', 'max:100'],
        ]);

        $team->members()->syncWithoutDetaching([
            $data['user_id'] => ['role' => $data['role'] ?? null],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member added',
            'data' => $team->load('members:id,name'),
        ], 201);
    }

    public function removeMember(Team $team, int $user)
    {
        $team->members()->detach($user);

        return response()->json(['success' => true, 'message' => 'Member removed']);
    }
}
