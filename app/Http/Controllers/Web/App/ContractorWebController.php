<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App\Concerns\ResolvesCurrentProject;
use App\Models\Contractor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContractorWebController extends Controller
{
    use ResolvesCurrentProject;

    public function index(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Contractors/Index', $this->sharedProjects($request, $project) + [
            'contractors' => $project->contractors()->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Contractors/Create', $this->sharedProjects($request, $project));
    }

    public function store(Request $request): RedirectResponse
    {
        $project = $this->currentProject($request);
        $contractor = $project->contractors()->create($request->validate($this->rules()));

        return redirect()->route('app.contractors.show', $contractor)->with('success', 'Исполнитель создан.');
    }

    public function show(Request $request, Contractor $contractor): Response
    {
        $this->authorize('view', $contractor);
        $entries = $contractor->serviceEntries()->latest('date')->get();

        return Inertia::render('App/Contractors/Show', $this->sharedProjects($request, $contractor->project) + [
            'contractor' => $contractor,
            'totals' => [
                'total_amount' => (float) $entries->sum('total_amount'),
                'paid_amount' => (float) $entries->sum('paid_amount'),
                'debt' => (float) $entries->sum('total_amount') - (float) $entries->sum('paid_amount'),
            ],
            'services' => $entries,
        ]);
    }

    public function edit(Request $request, Contractor $contractor): Response
    {
        $this->authorize('update', $contractor);

        return Inertia::render('App/Contractors/Edit', $this->sharedProjects($request, $contractor->project) + [
            'contractor' => $contractor,
        ]);
    }

    public function update(Request $request, Contractor $contractor): RedirectResponse
    {
        $this->authorize('update', $contractor);
        $contractor->update($request->validate($this->rules()));

        return redirect()->route('app.contractors.show', $contractor)->with('success', 'Исполнитель обновлён.');
    }

    public function destroy(Contractor $contractor): RedirectResponse
    {
        $this->authorize('delete', $contractor);
        $contractor->delete();

        return redirect()->route('app.contractors.index')->with('success', 'Исполнитель удалён.');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'comment' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
