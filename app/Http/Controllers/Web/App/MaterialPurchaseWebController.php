<?php

namespace App\Http\Controllers\Web\App;

use App\Actions\CreateMaterialPurchaseAction;
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
            'purchases' => $project->materialPurchases()->with('tags')->latest('date')->paginate(15),
        ]);
    }

    public function create(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Purchases/Create', $this->sharedProjects($request, $project) + [
            'materials' => $project->materials()->with('unit')->orderBy('name')->get(),
            'tags' => $project->tags()->orderBy('name')->get(),
        ]);
    }

    public function store(MaterialPurchaseRequest $request, CreateMaterialPurchaseAction $action): RedirectResponse
    {
        $purchase = $action->execute($request->validated());

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

        return Inertia::render('App/Purchases/Edit', $this->sharedProjects($request, $purchase->project) + [
            'purchase' => $purchase->load(['items.material.unit', 'tags']),
            'materials' => $purchase->project->materials()->with('unit')->orderBy('name')->get(),
            'tags' => $purchase->project->tags()->orderBy('name')->get(),
        ]);
    }
}
