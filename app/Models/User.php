<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
#[Fillable(['name', 'email', 'password','avatar','phone','job_title','bio','availability','years_of_expereince','current_team'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use hasApiTokens,HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    //Projects created by the user
    public function ownedProjects()
    {
        return $this->hasMany(Project::class,'owner_id');
    }
    //The teams he is a member of
    public function teams(){
        return $this->belongsToMany(Team::class.'team_members')->withPivot('role')->withTimestamps();
    }
    // Tasks assigned to the user
    public function tasks()
    {
    return $this->belongsToMany(Task::class, 'task_assignees')->withTimestamps();
    }
    //The files he uploaded
    public function files(){
        return $this->hasmany(ProjectFile::class,'uploaded_by');
    }






}
