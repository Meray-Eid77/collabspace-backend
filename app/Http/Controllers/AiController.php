<?php

namespace App\Http\Controllers;

use App\Models\AiMessage;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function index(Request $request)
    {
        $messages = AiMessage::where('user_id', $request->user()->id)->oldest()->get();

        return view('ai.index', compact('messages'));
    }

    public function ask(Request $request)
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:1000']]);

        AiMessage::create(['user_id' => $request->user()->id, 'role' => 'user', 'content' => $data['message']]);
        AiMessage::create([
            'user_id' => $request->user()->id,
            'role' => 'assistant',
            'content' => $this->reply($data['message']),
        ]);

        return redirect('/ai');
    }

    public function clear(Request $request)
    {
        AiMessage::where('user_id', $request->user()->id)->delete();

        return redirect('/ai');
    }

    // رد تجريبي مبني على بيانات حقيقية
    private function reply(string $question): string
    {
        $q = strtolower($question);

        if (str_contains($q, 'task') || str_contains($q, 'pending') || str_contains($q, 'progress')) {
            $todo = Task::where('status', 'todo')->count();
            $doing = Task::whereIn('status', ['in_progress', 'in_review'])->count();
            $done = Task::where('status', 'completed')->count();

            return "You have {$todo} pending tasks, {$doing} in progress or review, and {$done} completed.";
        }

        if (str_contains($q, 'project')) {
            $active = Project::where('status', 'active')->pluck('name')->implode(', ');

            return 'Active projects: ' . ($active ?: 'none') . '.';
        }

        if (str_contains($q, 'meeting')) {
            $next = Meeting::where('start_at', '>=', now())->orderBy('start_at')->first();

            return $next
                ? "Your next meeting is \"{$next->title}\" on {$next->start_at->format('M d, H:i')}."
                : 'You have no upcoming meetings.';
        }

        return 'I can summarize your tasks, projects, and meetings. Try asking: "How many tasks are pending?"';
    }
}
