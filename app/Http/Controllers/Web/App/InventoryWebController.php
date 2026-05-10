<?php

namespace App\Http\Controllers\Web\App;

use App\Actions\CreateInventoryAdjustmentAction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App\Concerns\ResolvesCurrentProject;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryWebController extends Controller
{
    use ResolvesCurrentProject;

    public function __invoke(Request $request, InventoryService $inventory): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Inventory/Index', $this->sharedProjects($request, $project) + [
            'inventory' => $inventory->getInventoryTable($project, $request->all()),
            'filters' => $request->only(['search', 'only_positive', 'only_problem']),
        ]);
    }

    public function createAdjustment(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Inventory/AdjustmentCreate', $this->sharedProjects($request, $project) + [
            'materials' => $project->materials()->with('unit')->orderBy('name')->get(),
        ]);
    }

    public function storeAdjustment(Request $request, CreateInventoryAdjustmentAction $action): RedirectResponse
    {
        $project = $this->currentProject($request);
        $data = $request->validate([
            'material_id' => ['required', 'exists:materials,id'],
            'date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'not_in:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['nullable', 'numeric'],
            'comment' => ['nullable', 'string'],
        ]);

        $action->execute(array_merge($data, ['project_id' => $project->id, 'type' => 'adjustment']));

        return redirect()->route('app.inventory.index')->with('success', 'Корректировка склада сохранена.');
    }
}
