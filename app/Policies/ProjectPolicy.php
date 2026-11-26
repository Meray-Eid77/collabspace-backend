<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    // صاحب المشروع أو عضو بأحد فرقه
    public function view(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id
            || $project->teams()->whereHas('members', fn ($q) => $q->where('users.id', $user->id))->exists();
    }

    // التعديل والحذف لصاحب المشروع بس
    public function update(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id;
    }
}
