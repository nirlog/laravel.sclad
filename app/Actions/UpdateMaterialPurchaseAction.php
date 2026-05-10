<?php

namespace App\Actions;

use App\Actions\Concerns\ValidatesProjectScope;
use App\Models\InventoryMovement;
use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseItem;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class UpdateMaterialPurchaseAction
{
    use ValidatesProjectScope;

    public function __construct(private InventoryService $inventory) {}

    public function execute(MaterialPurchase $purchase, array $data): MaterialPurchase
    {
        return DB::transaction(function () use ($purchase, $data): MaterialPurchase {
            $project = $purchase->project;
            $items = $data['items'];
            $tagIds = $data['tag_ids'] ?? [];
            $this->assertTagsBelongToProject($project, $tagIds);
            $purchase->loadMissing('items.material');

            $oldQuantities = $purchase->items
                ->groupBy('material_id')
                ->map(fn ($items) => (float) $items->sum('quantity'));
            $newQuantities = collect($items)
                ->groupBy('material_id')
                ->map(fn ($items) => (float) collect($items)->sum('quantity'));

            foreach ($newQuantities->keys() as $materialId) {
                if (! $project->materials()->whereKey($materialId)->exists()) {
                    throw ValidationException::withMessages([
                        'items' => 'Материал покупки не принадлежит выбранному проекту.',
                    ]);
                }
            }

            foreach ($oldQuantities->keys()->merge($newQuantities->keys())->unique() as $materialId) {
                $material = $project->materials()->findOrFail($materialId);
                $projectedStock = $this->inventory->getCurrentStock($project, $material)
                    - (float) ($oldQuantities[$materialId] ?? 0)
                    + (float) ($newQuantities[$materialId] ?? 0);

                if ($projectedStock < -0.0001) {
                    throw new RuntimeException("Нельзя обновить покупку: по материалу «{$material->name}» остаток станет отрицательным.");
                }
            }

            InventoryMovement::where('source_type', MaterialPurchaseItem::class)
                ->whereIn('source_id', $purchase->items->pluck('id'))
                ->delete();
            $purchase->items()->delete();

            unset($data['items'], $data['tag_ids']);
            $data['project_id'] = $project->id;
            $data['total_amount'] = 0;
            $purchase->update($data);

            $total = 0.0;
            foreach ($items as $item) {
                $material = $project->materials()->findOrFail($item['material_id']);
                $line = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
                $purchaseItem = $purchase->items()->create([
                    'material_id' => $material->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $line,
                ]);

                InventoryMovement::create([
                    'project_id' => $project->id,
                    'material_id' => $material->id,
                    'date' => $purchase->date,
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $line,
                    'source_type' => $purchaseItem::class,
                    'source_id' => $purchaseItem->id,
                    'comment' => 'Покупка материалов #'.$purchase->id,
                ]);

                $total += $line;
            }

            $purchase->update(['total_amount' => round($total, 2)]);
            $purchase->tags()->sync($tagIds);

            return $purchase->refresh()->load('items', 'tags');
        });
    }
}
