<?php

namespace App\Http\Controllers\Web\App;

use App\Actions\CreateMaterialWriteOffAction;
use App\Actions\DeleteMaterialWriteOffAction;
use App\Actions\UpdateMaterialWriteOffAction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App\Concerns\ResolvesCurrentProject;
use App\Http\Requests\MaterialWriteOffRequest;
use App\Models\MaterialWriteOff;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class MaterialWriteOffWebController extends Controller
{
    use ResolvesCurrentProject;

    public function index(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/WriteOffs/Index', $this->sharedProjects($request, $project) + [
            'writeOffs' => $project->materialWriteOffs()->with(['material.unit', 'tags'])->latest('date')->paginate(15),
        ]);
    }

    public function create(Request $request, InventoryService $inventory): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/WriteOffs/Create', $this->formData($request, $project, $inventory));
    }

    public function store(MaterialWriteOffRequest $request, CreateMaterialWriteOffAction $action): RedirectResponse
    {
        $project = $this->currentProject($request);

        try {
            $writeOff = $action->execute(array_merge($request->validated(), ['project_id' => $project->id]));
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('app.write-offs.show', $writeOff)->with('success', 'Списание сохранено. Складской остаток уменьшен.');
    }

    public function show(Request $request, MaterialWriteOff $writeOff): Response
    {
        $this->authorize('view', $writeOff);

        return Inertia::render('App/WriteOffs/Show', $this->sharedProjects($request, $writeOff->project) + [
            'writeOff' => $writeOff->load(['material.unit', 'tags']),
        ]);
    }

    public function edit(Request $request, MaterialWriteOff $writeOff, InventoryService $inventory): Response
    {
        $this->authorize('update', $writeOff);

        return Inertia::render('App/WriteOffs/Edit', $this->formData($request, $writeOff->project, $inventory) + [
            'writeOff' => $writeOff->load(['material.unit', 'tags']),
        ]);
    }

    public function update(MaterialWriteOffRequest $request, MaterialWriteOff $writeOff, UpdateMaterialWriteOffAction $action): RedirectResponse
    {
        $this->authorize('update', $writeOff);

        try {
            $updated = $action->execute($writeOff, array_merge($request->validated(), ['project_id' => $writeOff->project_id]));
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('app.write-offs.show', $updated)->with('success', 'Списание обновлено.');
    }

    public function destroy(MaterialWriteOff $writeOff, DeleteMaterialWriteOffAction $action): RedirectResponse
    {
        $this->authorize('delete', $writeOff);
        $action->execute($writeOff);

        return redirect()->route('app.write-offs.index')->with('success', 'Списание удалено.');
    }

    private function formData(Request $request, $project, InventoryService $inventory): array
    {
        $materials = $project->materials()->with('unit')->orderBy('name')->get()
            ->map(fn ($material) => $material->setAttribute('current_stock', $inventory->getCurrentStock($project, $material)));

        return $this->sharedProjects($request, $project) + [
            'materials' => $materials,
            'tags' => $project->tags()->orderBy('name')->get(),
        ];
    }
}
