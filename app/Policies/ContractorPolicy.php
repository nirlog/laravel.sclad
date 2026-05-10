<?php
namespace App\Policies;
use App\Models\{Contractor,User};
class ContractorPolicy{public function view(User $user,Contractor $model): bool{return $user->id === $model->project->user_id;} public function update(User $user,Contractor $model): bool{return $this->view($user,$model);} public function delete(User $user,Contractor $model): bool{return $this->view($user,$model);} }
