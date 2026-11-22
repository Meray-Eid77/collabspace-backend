<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'description', 'type', 'priority', 'status',
    'start_date', 'deadline', 'owner_id',
])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'deadline' => 'date',
        ];
    }

    // Project Owner

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

   // Project Teams
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

   // Project Tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    // Project files
    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }
}
