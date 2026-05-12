<?php

namespace App\Actions;

use App\Models\InventoryMovement;
use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseItem;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeleteMaterialPurchaseAction
{
    public function __construct(private InventoryService $inventory) {}

    public function execute(MaterialPurchase $purchase): void
    {
        DB::transaction(function () use ($purchase): void {
            $purchase->loadMissing('items.material');

            $oldMovementIds = InventoryMovement::where('source_type', MaterialPurchaseItem::class)
                ->whereIn('source_id', $purchase->items->pluck('id'))
                ->pluck('id')
                ->all();

            foreach ($purchase->items->groupBy('material_id') as $items) {
                $material = $items->first()->material;

                if ($this->inventory->hasNegativeStockInHistory($purchase->project, $material, $oldMovementIds)) {
                    throw new RuntimeException("Нельзя удалить покупку: по материалу «{$material->name}» остаток станет отрицательным в истории операций.");
                }
            }

            InventoryMovement::where('source_type', MaterialPurchaseItem::class)
                ->whereIn('source_id', $purchase->items->pluck('id'))
                ->delete();
            $purchase->tags()->detach();
            $purchase->delete();
        });
    }
}
