<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateMaterialWriteOffAction;
use App\Actions\DeleteMaterialWriteOffAction;
use App\Actions\UpdateMaterialWriteOffAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialWriteOffRequest;
use App\Models\MaterialWriteOff;
use App\Models\Project;

class MaterialWriteOffController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        return $project->materialWriteOffs()->with('material', 'tags')->latest('date')->get();
    }

    public function store(MaterialWriteOffRequest $request, Project $project, CreateMaterialWriteOffAction $action)
    {
        $this->authorize('update', $project);

        return $action->execute(array_merge($request->validated(), ['project_id' => $project->id]));
    }

    public function show(MaterialWriteOff $writeOff)
    {
        $this->authorize('view', $writeOff);

        return $writeOff->load('material', 'tags');
    }

    public function update(MaterialWriteOffRequest $request, MaterialWriteOff $writeOff, UpdateMaterialWriteOffAction $action)
    {
        $this->authorize('update', $writeOff);

        return $action->execute($writeOff, array_merge($request->validated(), ['project_id' => $writeOff->project_id]));
    }

    public function destroy(MaterialWriteOff $writeOff, DeleteMaterialWriteOffAction $action)
    {
        $this->authorize('delete', $writeOff);
        $action->execute($writeOff);

        return response()->noContent();
    }
}
