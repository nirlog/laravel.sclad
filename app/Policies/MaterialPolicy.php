<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\User;

class MaterialPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, Material $model): bool
    {
        return $user->id === $model->project->user_id;
    }

    public function update(User $user, Material $model): bool
    {
        return $this->view($user, $model);
    }

    public function delete(User $user, Material $model): bool
    {
        return $this->view($user, $model);
    }
}
