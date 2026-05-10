<?php

namespace App\Actions;

use App\Models\InventoryMovement;
use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseItem;
use Illuminate\Support\Facades\DB;

class UpdateMaterialPurchaseAction
{
    public function execute(MaterialPurchase $purchase, array $data): MaterialPurchase
    {
        return DB::transaction(function () use ($purchase, $data): MaterialPurchase {
            InventoryMovement::where('source_type', MaterialPurchaseItem::class)
                ->whereIn('source_id', $purchase->items()->pluck('id'))
                ->delete();

            $purchase->items()->delete();
            $purchase->tags()->detach();
            $purchase->delete();

            $data['project_id'] = $purchase->project_id;

            return app(CreateMaterialPurchaseAction::class)->execute($data);
        });
    }
}
