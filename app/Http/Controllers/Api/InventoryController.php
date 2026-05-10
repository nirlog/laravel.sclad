<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateInventoryAdjustmentAction;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Project $project, InventoryService $service)
    {
        $this->authorize('view', $project);

        return $service->getInventoryTable($project);
    }

    public function movements(Project $project)
    {
        $this->authorize('view', $project);

        return $project->inventoryMovements()->with('material', 'tags')->latest('date')->get();
    }

    public function adjustment(Request $request, Project $project, CreateInventoryAdjustmentAction $action)
    {
        $this->authorize('update', $project);
        $data = $request->validate([
            'material_id' => ['required', 'exists:materials,id'],
            'date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'not_in:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['nullable', 'numeric'],
            'comment' => ['nullable', 'string'],
        ]);

        return $action->execute(array_merge($data, ['project_id' => $project->id, 'type' => 'adjustment']));
    }
}
