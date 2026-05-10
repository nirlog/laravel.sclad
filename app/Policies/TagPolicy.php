<?php
namespace App\Policies;
use App\Models\{Tag,User};
class TagPolicy{public function view(User $user,Tag $model): bool{return $user->id === $model->project->user_id;} public function update(User $user,Tag $model): bool{return $this->view($user,$model);} public function delete(User $user,Tag $model): bool{return $this->view($user,$model);} }
