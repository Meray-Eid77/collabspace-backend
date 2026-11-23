<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['meeting_id', 'speaker_id', 'timestamp', 'text'])]
class MeetingTranscript extends Model
{
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function speaker()
    {
        return $this->belongsTo(User::class, 'speaker_id');
    }
}
