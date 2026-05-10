<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, Tag $model): bool
    {
        return $user->id === $model->project->user_id;
    }

    public function update(User $user, Tag $model): bool
    {
        return $this->view($user, $model);
    }

    public function delete(User $user, Tag $model): bool
    {
        return $this->view($user, $model);
    }
}
