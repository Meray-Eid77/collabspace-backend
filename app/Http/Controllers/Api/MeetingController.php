<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $weekStart = $request->week ? Carbon::parse($request->week)->startOfWeek() : now()->startOfWeek();

        $meetings = Meeting::with('participants:id,name')
            ->whereBetween('start_at', [$weekStart, $weekStart->copy()->endOfWeek()])
            ->orderBy('start_at')
            ->get();

        return response()->json(['success' => true, 'data' => $meetings]);
    }

    public function upcoming()
    {
        return response()->json([
            'success' => true,
            'data' => Meeting::with('project:id,name')->where('start_at', '>=', now())->orderBy('start_at')->take(5)->get(),
        ]);
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
        $meeting->participants()->attach(collect($data['participants'] ?? [])->push($request->user()->id)->unique());

        return response()->json(['success' => true, 'message' => 'Meeting created', 'data' => $meeting->load('participants:id,name')], 201);
    }

    public function show(Request $request, Meeting $meeting)
    {
        $meeting->load(['project:id,name', 'participants:id,name']);

        $transcript = $meeting->transcripts()->with('speaker:id,name')
            ->when($request->search, fn ($q, $s) => $q->where('text', 'like', "%{$s}%"))
            ->orderBy('id')->get();

        return response()->json(['success' => true, 'data' => ['meeting' => $meeting, 'transcript' => $transcript]]);
    }

    public function summary(Meeting $meeting)
    {
        $lines = $meeting->transcripts()->take(5)->pluck('text');

        $meeting->update([
            'summary' => $lines->isEmpty()
                ? "Meeting \"{$meeting->title}\" was held. No transcript available."
                : "Key points discussed in \"{$meeting->title}\":\n- " . $lines->map(fn ($l) => Str::limit($l, 90))->implode("\n- "),
        ]);

        return response()->json(['success' => true, 'message' => 'Summary generated', 'data' => ['summary' => $meeting->summary]]);
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return response()->json(['success' => true, 'message' => 'Meeting deleted']);
    }
}
