<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['project_id', 'created_by', 'title', 'description', 'start_at', 'end_at', 'link', 'summary'])]
class Meeting extends Model
{
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'meeting_participants')->withTimestamps();
    }

    public function transcripts()
    {
        return $this->hasMany(MeetingTranscript::class);
    }
}
