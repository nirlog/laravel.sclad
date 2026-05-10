<?php

namespace App\Actions;

use App\Actions\Concerns\ValidatesProjectScope;
use App\Models\InventoryMovement;
use App\Models\MaterialWriteOff;
use App\Models\Project;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateMaterialWriteOffAction
{
    use ValidatesProjectScope;

    public function __construct(private InventoryService $inventory) {}

    public function execute(array $data): MaterialWriteOff
    {
        return DB::transaction(function () use ($data): MaterialWriteOff {
            $project = Project::findOrFail($data['project_id']);
            $material = $project->materials()->find($data['material_id']);

            if (! $material) {
                throw ValidationException::withMessages([
                    'material_id' => 'Материал не принадлежит выбранному проекту.',
                ]);
            }

            $tagIds = $data['tag_ids'] ?? [];
            $this->assertTagsBelongToProject($project, $tagIds);
            $this->inventory->assertCanWriteOff($project, $material, (float) $data['quantity']);

            $unit = $this->inventory->getAverageUnitCost($project, $material);
            $total = round((float) $data['quantity'] * $unit, 2);
            unset($data['tag_ids']);
            $data['project_id'] = $project->id;
            $data['material_id'] = $material->id;

            $writeOff = MaterialWriteOff::create($data + [
                'unit_price' => $unit,
                'total_amount' => $total,
            ]);

            InventoryMovement::create([
                'project_id' => $project->id,
                'material_id' => $material->id,
                'date' => $writeOff->date,
                'type' => 'out',
                'quantity' => $writeOff->quantity,
                'unit_price' => $unit,
                'amount' => $total,
                'source_type' => $writeOff::class,
                'source_id' => $writeOff->id,
                'comment' => $writeOff->comment,
            ]);

            $writeOff->tags()->sync($tagIds);

            return $writeOff->load('tags');
        });
    }
}
