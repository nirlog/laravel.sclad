<?php
namespace App\Policies;
use App\Models\{Material,User};
class MaterialPolicy{public function view(User $user,Material $model): bool{return $user->id === $model->project->user_id;} public function update(User $user,Material $model): bool{return $this->view($user,$model);} public function delete(User $user,Material $model): bool{return $this->view($user,$model);} }
