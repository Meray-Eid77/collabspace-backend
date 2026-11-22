<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'project_id', 'uploaded_by', 'name', 'path', 'mime_type', 'size',
])]
class ProjectFile extends Model
{
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

   // The person who uploaded the file
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
