<?php
namespace App\Policies;
use App\Models\{MaterialPurchase,User};
class MaterialPurchasePolicy{public function view(User $user,MaterialPurchase $model): bool{return $user->id === $model->project->user_id;} public function update(User $user,MaterialPurchase $model): bool{return $this->view($user,$model);} public function delete(User $user,MaterialPurchase $model): bool{return $this->view($user,$model);} }
