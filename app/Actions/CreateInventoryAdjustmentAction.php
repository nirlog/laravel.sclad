<?php
namespace App\Actions;
use App\Models\InventoryMovement;use Illuminate\Support\Facades\DB;
class CreateInventoryAdjustmentAction{public function execute(array $data): InventoryMovement{return DB::transaction(fn()=>InventoryMovement::create($data+['type'=>'adjustment']));}}
