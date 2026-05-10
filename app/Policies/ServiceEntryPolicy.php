<?php
namespace App\Policies;
use App\Models\{ServiceEntry,User};
class ServiceEntryPolicy{public function view(User $user,ServiceEntry $model): bool{return $user->id === $model->project->user_id;} public function update(User $user,ServiceEntry $model): bool{return $this->view($user,$model);} public function delete(User $user,ServiceEntry $model): bool{return $this->view($user,$model);} }
