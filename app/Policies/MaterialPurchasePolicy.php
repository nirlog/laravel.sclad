<?php

namespace App\Policies;

use App\Models\MaterialPurchase;
use App\Models\User;

class MaterialPurchasePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, MaterialPurchase $model): bool
    {
        return $user->id === $model->project->user_id;
    }

    public function update(User $user, MaterialPurchase $model): bool
    {
        return $this->view($user, $model);
    }

    public function delete(User $user, MaterialPurchase $model): bool
    {
        return $this->view($user, $model);
    }
}
