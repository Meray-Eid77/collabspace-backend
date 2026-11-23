<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\ProjectFile;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::withCount('files')
            ->with('teams.members')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:100'],
            'priority' => ['required', 'in:low,medium,high'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $data['owner_id'] = $request->user()->id;
        Project::create($data);

        return redirect('/projects')->with('success', 'Project created');
    }

  public function show(Request $request, Project $project)
{
    $project->load(['owner', 'teams.members', 'files.uploader', 'tasks.assignees']);

    $tab = $request->get('tab', 'overview');

    $total = $project->tasks->count();
    $stats = [
        'total' => $total,
        'todo' => $project->tasks->where('status', 'todo')->count(),
        'in_progress' => $project->tasks->where('status', 'in_progress')->count(),
        'completed' => $project->tasks->where('status', 'completed')->count(),
    ];
    $stats['percent'] = $total ? round($stats['completed'] / $total * 100) : 0;

    $users = User::orderBy('name')->get();

    return view('projects.show', compact('project', 'tab', 'stats', 'users'));
}

public function storeTeam(Request $request, Project $project)
{
    $data = $request->validate(['name' => ['required', 'string', 'max:255']]);
    $project->teams()->create($data);

    return redirect("/projects/{$project->id}?tab=teams")->with('success', 'Team created');
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

    return redirect("/projects/{$team->project_id}?tab=teams")->with('success', 'Member added');
}

public function removeMember(Team $team, User $user)
{
    $team->members()->detach($user->id);

    return redirect("/projects/{$team->project_id}?tab=teams")->with('success', 'Member removed');
}

public function uploadFile(Request $request, Project $project)
{
    $request->validate(['file' => ['required', 'file', 'max:10240']]); // 10MB

    $file = $request->file('file');
    $path = $file->store("projects/{$project->id}", 'local');

    $project->files()->create([
        'uploaded_by' => $request->user()->id,
        'name' => $file->getClientOriginalName(),
        'path' => $path,
        'mime_type' => $file->getClientMimeType(),
        'size' => $file->getSize(),
    ]);

    return redirect("/projects/{$project->id}?tab=files")->with('success', 'File uploaded');
}

public function downloadFile(ProjectFile $file)
{
    return Storage::disk('local')->download($file->path, $file->name);
}
}
