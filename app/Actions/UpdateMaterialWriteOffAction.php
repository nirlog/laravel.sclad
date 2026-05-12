<?php

namespace App\Actions;

use App\Actions\Concerns\ValidatesProjectScope;
use App\Models\InventoryMovement;
use App\Models\MaterialWriteOff;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class UpdateMaterialWriteOffAction
{
    use ValidatesProjectScope;

    public function __construct(private InventoryService $inventory) {}

    public function execute(MaterialWriteOff $writeOff, array $data): MaterialWriteOff
    {
        return DB::transaction(function () use ($writeOff, $data): MaterialWriteOff {
            $project = $writeOff->project;
            $material = $project->materials()->find($data['material_id']);

            if (! $material) {
                throw ValidationException::withMessages([
                    'material_id' => 'Материал не принадлежит выбранному проекту.',
                ]);
            }

            $tagIds = $data['tag_ids'] ?? [];
            $this->assertTagsBelongToProject($project, $tagIds);

            $available = $this->inventory->getCurrentStock($project, $material);
            if ((int) $writeOff->material_id === (int) $material->id) {
                $available += (float) $writeOff->quantity;
            }

            if ($available + 0.0001 < (float) $data['quantity']) {
                throw new RuntimeException('Недостаточно материала на складе для обновления списания.');
            }

            $unit = $this->inventory->getAverageUnitCost($project, $material, Carbon::parse($data['date']));
            $total = round((float) $data['quantity'] * $unit, 2);
            unset($data['tag_ids']);

            $writeOff->update(array_merge($data, [
                'project_id' => $project->id,
                'material_id' => $material->id,
                'unit_price' => $unit,
                'total_amount' => $total,
            ]));

            InventoryMovement::updateOrCreate(
                ['source_type' => $writeOff::class, 'source_id' => $writeOff->id],
                [
                    'project_id' => $project->id,
                    'material_id' => $material->id,
                    'date' => $writeOff->date,
                    'type' => 'out',
                    'quantity' => $writeOff->quantity,
                    'unit_price' => $unit,
                    'amount' => $total,
                    'comment' => $writeOff->comment,
                ]
            );

            $writeOff->tags()->sync($tagIds);

            return $writeOff->refresh()->load('tags');
        });
    }
}
