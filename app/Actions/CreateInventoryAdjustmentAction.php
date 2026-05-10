<?php

namespace App\Actions;

use App\Models\InventoryMovement;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateInventoryAdjustmentAction
{
    public function execute(array $data): InventoryMovement
    {
        return DB::transaction(function () use ($data): InventoryMovement {
            $project = Project::findOrFail($data['project_id']);

            if (! $project->materials()->whereKey($data['material_id'])->exists()) {
                throw ValidationException::withMessages([
                    'material_id' => 'Материал корректировки не принадлежит выбранному проекту.',
                ]);
            }

            $data['project_id'] = $project->id;

            return InventoryMovement::create($data + ['type' => 'adjustment']);
        });
    }
}
