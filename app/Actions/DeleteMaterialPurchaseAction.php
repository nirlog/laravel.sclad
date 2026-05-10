<?php
namespace App\Actions;
use App\Models\{InventoryMovement,MaterialPurchase,MaterialPurchaseItem};use Illuminate\Support\Facades\DB;
class DeleteMaterialPurchaseAction{public function execute(MaterialPurchase $purchase): void{DB::transaction(function() use($purchase){InventoryMovement::where('source_type',MaterialPurchaseItem::class)->whereIn('source_id',$purchase->items()->pluck('id'))->delete();$purchase->tags()->detach();$purchase->delete();});}}
