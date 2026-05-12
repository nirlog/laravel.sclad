<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateMaterialPurchaseAction;
use App\Actions\DeleteMaterialPurchaseAction;
use App\Actions\UpdateMaterialPurchaseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialPurchaseRequest;
use App\Models\MaterialPurchase;
use App\Models\Project;

class MaterialPurchaseController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        return $project->materialPurchases()->with('items.material', 'tags')->latest('date')->get();
    }

    public function store(MaterialPurchaseRequest $request, Project $project, CreateMaterialPurchaseAction $action)
    {
        $this->authorize('update', $project);

        return $action->execute(array_merge($request->validated(), ['project_id' => $project->id]));
    }

    public function show(MaterialPurchase $purchase)
    {
        $this->authorize('view', $purchase);

        return $purchase->load('items.material', 'tags');
    }

    public function update(MaterialPurchaseRequest $request, MaterialPurchase $purchase, UpdateMaterialPurchaseAction $action)
    {
        $this->authorize('update', $purchase);

        return $action->execute($purchase, array_merge($request->validated(), ['project_id' => $purchase->project_id]));
    }

    public function destroy(MaterialPurchase $purchase, DeleteMaterialPurchaseAction $action)
    {
        $this->authorize('delete', $purchase);
        $action->execute($purchase);

        return response()->noContent();
    }
}
