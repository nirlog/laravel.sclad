<?php

namespace App\Http\Controllers\Web\App;

use App\Actions\CreateServiceEntryAction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App\Concerns\ResolvesCurrentProject;
use App\Http\Requests\ServiceEntryRequest;
use App\Models\ServiceEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceEntryWebController extends Controller
{
    use ResolvesCurrentProject;

    public function index(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Services/Index', $this->sharedProjects($request, $project) + [
            'services' => $project->serviceEntries()->with(['contractor', 'tags'])->latest('date')->paginate(15),
        ]);
    }

    public function create(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Services/Create', $this->sharedProjects($request, $project) + [
            'contractors' => $project->contractors()->orderBy('name')->get(),
            'tags' => $project->tags()->orderBy('name')->get(),
        ]);
    }

    public function store(ServiceEntryRequest $request, CreateServiceEntryAction $action): RedirectResponse
    {
        $entry = $action->execute($request->validated());

        return redirect()->route('app.services.show', $entry)->with('success', 'Услуга сохранена. Расходы обновлены.');
    }

    public function show(Request $request, ServiceEntry $service): Response
    {
        $this->authorize('view', $service);

        return Inertia::render('App/Services/Show', $this->sharedProjects($request, $service->project) + [
            'service' => $service->load(['contractor', 'tags']),
        ]);
    }

    public function edit(Request $request, ServiceEntry $service): Response
    {
        $this->authorize('update', $service);

        return Inertia::render('App/Services/Edit', $this->sharedProjects($request, $service->project) + [
            'service' => $service->load(['contractor', 'tags']),
            'contractors' => $service->project->contractors()->orderBy('name')->get(),
            'tags' => $service->project->tags()->orderBy('name')->get(),
        ]);
    }
}
