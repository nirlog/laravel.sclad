<?php

namespace App\Http\Controllers\Web\App;

use App\Actions\CreateMaterialPurchaseAction;
use App\Actions\DeleteMaterialPurchaseAction;
use App\Actions\UpdateMaterialPurchaseAction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App\Concerns\ResolvesCurrentProject;
use App\Http\Requests\MaterialPurchaseRequest;
use App\Models\MaterialPurchase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MaterialPurchaseWebController extends Controller
{
    use ResolvesCurrentProject;

    public function index(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Purchases/Index', $this->sharedProjects($request, $project) + [
            'purchases' => $project->materialPurchases()->with(['items.material.unit', 'tags'])->latest('date')->paginate(15),
        ]);
    }

    public function create(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Purchases/Create', $this->formData($request, $project));
    }

    public function store(MaterialPurchaseRequest $request, CreateMaterialPurchaseAction $action): RedirectResponse
    {
        $project = $this->currentProject($request);
        $purchase = $action->execute(array_merge($request->validated(), ['project_id' => $project->id]));

        return redirect()->route('app.purchases.show', $purchase)->with('success', 'Покупка сохранена. Остаток материала обновлён.');
    }

    public function show(Request $request, MaterialPurchase $purchase): Response
    {
        $this->authorize('view', $purchase);

        return Inertia::render('App/Purchases/Show', $this->sharedProjects($request, $purchase->project) + [
            'purchase' => $purchase->load(['items.material.unit', 'tags']),
        ]);
    }

    public function edit(Request $request, MaterialPurchase $purchase): Response
    {
        $this->authorize('update', $purchase);

        return Inertia::render('App/Purchases/Edit', $this->formData($request, $purchase->project) + [
            'purchase' => $purchase->load(['items.material.unit', 'tags']),
        ]);
    }

    public function update(MaterialPurchaseRequest $request, MaterialPurchase $purchase, UpdateMaterialPurchaseAction $action): RedirectResponse
    {
        $this->authorize('update', $purchase);
        $updated = $action->execute($purchase, array_merge($request->validated(), ['project_id' => $purchase->project_id]));

        return redirect()->route('app.purchases.show', $updated)->with('success', 'Покупка обновлена.');
    }

    public function destroy(MaterialPurchase $purchase, DeleteMaterialPurchaseAction $action): RedirectResponse
    {
        $this->authorize('delete', $purchase);
        $action->execute($purchase);

        return redirect()->route('app.purchases.index')->with('success', 'Покупка удалена.');
    }

    private function formData(Request $request, $project): array
    {
        return $this->sharedProjects($request, $project) + [
            'materials' => $project->materials()->with('unit')->orderBy('name')->get(),
            'tags' => $project->tags()->orderBy('name')->get(),
        ];
    }
}
