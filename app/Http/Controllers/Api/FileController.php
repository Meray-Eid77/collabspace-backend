<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Project $project)
    {
        return response()->json([
            'success' => true,
            'data' => $project->files()->with('uploader:id,name')->latest()->get(),
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $request->validate(['file' => ['required', 'file', 'max:10240']]);

        $file = $request->file('file');
        $path = $file->store("projects/{$project->id}", 'local');

        $record = $project->files()->create([
            'uploaded_by' => $request->user()->id,
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json(['success' => true, 'message' => 'File uploaded', 'data' => $record], 201);
    }

    public function download(ProjectFile $file)
    {
        return response()->download(Storage::disk('local')->path($file->path), $file->name);
    }
}
