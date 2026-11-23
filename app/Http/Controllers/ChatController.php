<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $type = $request->get('type', 'direct');

        $conversations = $user->conversations()
            ->where('type', $type)
            ->with(['members', 'latestMessage.sender'])
            ->get()
            ->sortByDesc(fn ($c) => $c->latestMessage?->created_at);

        $active = null;
        $messages = collect();

        if ($request->conversation) {
            $active = $user->conversations()->with('members')->findOrFail($request->conversation);
            $messages = $active->messages()->with('sender')->oldest()->get();
        }

        $users = User::where('id', '!=', $user->id)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        return view('chats.index', compact('conversations', 'active', 'messages', 'type', 'users', 'projects'));
    }

    // بدء محادثة مباشرة مع مستخدم
    public function startDirect(Request $request)
    {
        $request->validate(['user_id' => ['required', 'exists:users,id']]);

        $me = $request->user();
        $otherId = (int) $request->user_id;

        $existing = $me->conversations()
            ->where('type', 'direct')
            ->whereHas('members', fn ($q) => $q->where('users.id', $otherId))
            ->first();

        if (! $existing) {
            $existing = Conversation::create(['type' => 'direct']);
            $existing->members()->attach([$me->id, $otherId]);
        }

        return redirect("/chats?type=direct&conversation={$existing->id}");
    }

    // إنشاء قناة لمشروع (بتضم أعضاء فرق المشروع + المنشئ)
    public function startProject(Request $request)
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $project = Project::with('teams.members')->findOrFail($data['project_id']);

        $conversation = Conversation::create([
            'type' => 'project',
            'name' => $data['name'] ?: $project->name . ' - Public Channel',
            'project_id' => $project->id,
        ]);

        $memberIds = $project->teams->flatMap->members->pluck('id')
            ->push($request->user()->id)
            ->unique();
        $conversation->members()->attach($memberIds);

        return redirect("/chats?type=project&conversation={$conversation->id}");
    }

    public function send(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->members()->where('users.id', $request->user()->id)->exists(), 403);

        $request->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        if (! $request->body && ! $request->hasFile('attachment')) {
            return back();
        }

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'body' => $request->body,
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $data['attachment_path'] = $file->store("chats/{$conversation->id}", 'local');
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        Message::create($data);

        return redirect("/chats?type={$conversation->type}&conversation={$conversation->id}");
    }

    public function downloadAttachment(Request $request, Message $message)
    {
        abort_unless(
            $message->conversation->members()->where('users.id', $request->user()->id)->exists(),
            403
        );

        return response()->download(
            Storage::disk('local')->path($message->attachment_path),
            $message->attachment_name
        );
    }
}
