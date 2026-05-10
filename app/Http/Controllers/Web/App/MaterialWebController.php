<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App\Concerns\ResolvesCurrentProject;
use App\Models\Material;
use App\Models\Unit;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MaterialWebController extends Controller
{
    use ResolvesCurrentProject;

    public function index(Request $request, InventoryService $inventory): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Materials/Index', $this->sharedProjects($request, $project) + [
            'materials' => $project->materials()->with('unit')->orderBy('name')->get()
                ->map(fn ($material) => $material->setAttribute('current_stock', $inventory->getCurrentStock($project, $material))),
        ]);
    }

    public function create(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Materials/Create', $this->sharedProjects($request, $project) + [
            'units' => Unit::orderBy('short_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $project = $this->currentProject($request);
        $material = $project->materials()->create($request->validate($this->rules()));

        return redirect()->route('app.materials.show', $material)->with('success', 'Материал создан.');
    }

    public function show(Request $request, Material $material, InventoryService $inventory): Response
    {
        $this->authorize('view', $material);

        return Inertia::render('App/Materials/Show', $this->sharedProjects($request, $material->project) + [
            'material' => $material->load('unit'),
            'stock' => $inventory->getCurrentStock($material->project, $material),
            'averageCost' => $inventory->getAverageUnitCost($material->project, $material),
            'movements' => $material->inventoryMovements()->latest('date')->paginate(20),
        ]);
    }

    public function edit(Request $request, Material $material): Response
    {
        $this->authorize('update', $material);

        return Inertia::render('App/Materials/Edit', $this->sharedProjects($request, $material->project) + [
            'material' => $material->load('unit'),
            'units' => Unit::orderBy('short_name')->get(),
        ]);
    }

    public function update(Request $request, Material $material): RedirectResponse
    {
        $this->authorize('update', $material);
        $material->update($request->validate($this->rules()));

        return redirect()->route('app.materials.show', $material)->with('success', 'Материал обновлён.');
    }

    public function destroy(Material $material): RedirectResponse
    {
        $this->authorize('delete', $material);
        $material->delete();

        return redirect()->route('app.materials.index')->with('success', 'Материал удалён.');
    }

    private function rules(): array
    {
        return [
            'unit_id' => ['required', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
