<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'type', 'note', 'start_date', 'end_date'])]
class Report extends Model
{
    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
