<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MeetingController extends Controller
{
    // التقويم الأسبوعي
    public function index(Request $request)
    {
        $weekStart = $request->week
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $meetings = Meeting::with('participants')
            ->whereBetween('start_at', [$weekStart, $weekEnd])
            ->orderBy('start_at')
            ->get()
            ->groupBy(fn ($m) => $m->start_at->format('Y-m-d'));

        $days = collect(range(0, 6))->map(fn ($i) => $weekStart->copy()->addDays($i));

        $users = User::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        return view('meetings.index', compact('meetings', 'days', 'weekStart', 'users', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'link' => ['nullable', 'string', 'max:255'],
            'participants' => ['nullable', 'array'],
            'participants.*' => ['exists:users,id'],
        ]);

        $data['created_by'] = $request->user()->id;
        $meeting = Meeting::create(collect($data)->except('participants')->all());

        $ids = collect($data['participants'] ?? [])->push($request->user()->id)->unique();
        $meeting->participants()->attach($ids);

        return redirect('/meetings')->with('success', 'Meeting created');
    }

    public function show(Request $request, Meeting $meeting)
    {
        $meeting->load(['project', 'participants', 'creator']);

        $transcripts = $meeting->transcripts()->with('speaker')
            ->when($request->search, fn ($q, $s) => $q->where('text', 'like', "%{$s}%"))
            ->orderBy('id')
            ->get();

        return view('meetings.show', compact('meeting', 'transcripts'));
    }

    // توليد الملخص (mock)
    public function summary(Meeting $meeting)
    {
        $lines = $meeting->transcripts()->take(5)->pluck('text');

        $meeting->update([
            'summary' => $lines->isEmpty()
                ? "Meeting \"{$meeting->title}\" was held with {$meeting->participants()->count()} participants. No transcript available."
                : "Key points discussed in \"{$meeting->title}\":\n- " . $lines->map(fn ($l) => \Illuminate\Support\Str::limit($l, 90))->implode("\n- "),
        ]);

        return back()->with('success', 'Summary generated');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return redirect('/meetings')->with('success', 'Meeting deleted');
    }
}
