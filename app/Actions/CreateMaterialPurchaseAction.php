<?php

namespace App\Actions;

use App\Actions\Concerns\ValidatesProjectScope;
use App\Models\InventoryMovement;
use App\Models\MaterialPurchase;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateMaterialPurchaseAction
{
    use ValidatesProjectScope;

    public function execute(array $data): MaterialPurchase
    {
        return DB::transaction(function () use ($data): MaterialPurchase {
            $project = Project::findOrFail($data['project_id']);
            $items = $data['items'];
            $tagIds = $data['tag_ids'] ?? [];
            $this->assertTagsBelongToProject($project, $tagIds);

            unset($data['items'], $data['tag_ids']);
            $data['project_id'] = $project->id;

            $purchase = MaterialPurchase::create($data + ['total_amount' => 0]);
            $total = 0.0;

            foreach ($items as $item) {
                $material = $project->materials()->find($item['material_id']);

                if (! $material) {
                    throw ValidationException::withMessages([
                        'items' => 'Материал покупки не принадлежит выбранному проекту.',
                    ]);
                }

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

            return $purchase->load('items', 'tags');
        });
    }
}
