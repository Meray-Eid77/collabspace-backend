<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Project;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $conversations = $request->user()->conversations()
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->with(['members:id,name', 'latestMessage'])
            ->get();

        return response()->json(['success' => true, 'data' => $conversations]);
    }

    public function messages(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->members()->where('users.id', $request->user()->id)->exists(), 403);

        return response()->json([
            'success' => true,
            'data' => $conversation->messages()->with('sender:id,name')->oldest()->get(),
        ]);
    }

    public function startDirect(Request $request)
    {
        $request->validate(['user_id' => ['required', 'exists:users,id']]);

        $me = $request->user();
        $otherId = (int) $request->user_id;

        $conversation = $me->conversations()->where('type', 'direct')
            ->whereHas('members', fn ($q) => $q->where('users.id', $otherId))
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create(['type' => 'direct']);
            $conversation->members()->attach([$me->id, $otherId]);
        }

        return response()->json(['success' => true, 'data' => $conversation->load('members:id,name')], 201);
    }

    public function startProject(Request $request)
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $project = Project::with('teams.members')->findOrFail($data['project_id']);

        $conversation = Conversation::create([
            'type' => 'project',
            'name' => $data['name'] ?? $project->name . ' - Public Channel',
            'project_id' => $project->id,
        ]);
        $conversation->members()->attach(
            $project->teams->flatMap->members->pluck('id')->push($request->user()->id)->unique()
        );

        return response()->json(['success' => true, 'data' => $conversation], 201);
    }

    public function send(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->members()->where('users.id', $request->user()->id)->exists(), 403);

        $request->validate([
            'body' => ['required_without:attachment', 'nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $data = ['conversation_id' => $conversation->id, 'sender_id' => $request->user()->id, 'body' => $request->body];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $data['attachment_path'] = $file->store("chats/{$conversation->id}", 'local');
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        return response()->json(['success' => true, 'data' => Message::create($data)], 201);
    }
}
