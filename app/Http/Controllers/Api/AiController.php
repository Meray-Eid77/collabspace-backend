<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiMessage;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => AiMessage::where('user_id', $request->user()->id)->oldest()->get(),
        ]);
    }

    public function chat(Request $request)
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:1000']]);

        AiMessage::create(['user_id' => $request->user()->id, 'role' => 'user', 'content' => $data['message']]);
        $reply = AiMessage::create([
            'user_id' => $request->user()->id,
            'role' => 'assistant',
            'content' => $this->reply($data['message']),
        ]);

        return response()->json(['success' => true, 'data' => $reply], 201);
    }

    public function clear(Request $request)
    {
        AiMessage::where('user_id', $request->user()->id)->delete();

        return response()->json(['success' => true, 'message' => 'Conversation cleared']);
    }

    private function reply(string $question): string
    {
        $q = strtolower($question);

        if (str_contains($q, 'task') || str_contains($q, 'pending') || str_contains($q, 'progress')) {
            return 'You have ' . Task::where('status', 'todo')->count() . ' pending tasks, '
                . Task::whereIn('status', ['in_progress', 'in_review'])->count() . ' in progress or review, and '
                . Task::where('status', 'completed')->count() . ' completed.';
        }
        if (str_contains($q, 'project')) {
            return 'Active projects: ' . (Project::where('status', 'active')->pluck('name')->implode(', ') ?: 'none') . '.';
        }
        if (str_contains($q, 'meeting')) {
            $next = Meeting::where('start_at', '>=', now())->orderBy('start_at')->first();

            return $next ? "Your next meeting is \"{$next->title}\" on {$next->start_at->format('M d, H:i')}." : 'You have no upcoming meetings.';
        }

        return 'I can summarize your tasks, projects, and meetings.';
    }
}
