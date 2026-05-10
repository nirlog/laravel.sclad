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

            foreach ($purchase->items->groupBy('material_id') as $materialId => $items) {
                $material = $items->first()->material;
                $incomingQuantity = (float) $items->sum('quantity');
                $projectedStock = $this->inventory->getCurrentStock($purchase->project, $material) - $incomingQuantity;

                if ($projectedStock < -0.0001) {
                    throw new RuntimeException("Нельзя удалить покупку: по материалу «{$material->name}» остаток станет отрицательным.");
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
