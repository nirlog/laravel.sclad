<?php
namespace App\Policies;
use App\Models\{MaterialWriteOff,User};
class MaterialWriteOffPolicy{public function view(User $user,MaterialWriteOff $model): bool{return $user->id === $model->project->user_id;} public function update(User $user,MaterialWriteOff $model): bool{return $this->view($user,$model);} public function delete(User $user,MaterialWriteOff $model): bool{return $this->view($user,$model);} }
