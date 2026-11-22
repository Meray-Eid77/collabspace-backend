<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['project_id', 'name'])]
class Team extends Model
{
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // أعضاء الفريق
    public function members()
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot('role')
            ->withTimestamps();
    }
}
