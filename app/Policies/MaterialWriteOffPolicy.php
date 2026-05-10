<?php

namespace App\Policies;

use App\Models\MaterialWriteOff;
use App\Models\User;

class MaterialWriteOffPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, MaterialWriteOff $model): bool
    {
        return $user->id === $model->project->user_id;
    }

    public function update(User $user, MaterialWriteOff $model): bool
    {
        return $this->view($user, $model);
    }

    public function delete(User $user, MaterialWriteOff $model): bool
    {
        return $this->view($user, $model);
    }
}
