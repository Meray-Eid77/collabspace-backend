<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'name', 'project_id'])]
class Conversation extends Model
{
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'conversation_members')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }


    public function displayName(int $userId): string
    {
        if ($this->type === 'project') {
            return $this->name ?? 'Channel';
        }

        return $this->members->firstWhere('id', '!=', $userId)?->name ?? 'Chat';
    }
}
