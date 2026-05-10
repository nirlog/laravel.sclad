<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateServiceEntryAction;
use App\Actions\UpdateServiceEntryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceEntryRequest;
use App\Models\Project;
use App\Models\ServiceEntry;

class ServiceEntryController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        return $project->serviceEntries()->with('contractor', 'tags')->latest('date')->get();
    }

    public function store(ServiceEntryRequest $request, Project $project, CreateServiceEntryAction $action)
    {
        $this->authorize('update', $project);

        return $action->execute(array_merge($request->validated(), ['project_id' => $project->id]));
    }

    public function show(ServiceEntry $serviceEntry)
    {
        $this->authorize('view', $serviceEntry);

        return $serviceEntry->load('contractor', 'tags');
    }

    public function update(ServiceEntryRequest $request, ServiceEntry $serviceEntry, UpdateServiceEntryAction $action)
    {
        $this->authorize('update', $serviceEntry);

        return $action->execute($serviceEntry, array_merge($request->validated(), ['project_id' => $serviceEntry->project_id]));
    }

    public function destroy(ServiceEntry $serviceEntry)
    {
        $this->authorize('delete', $serviceEntry);
        $serviceEntry->delete();

        return response()->noContent();
    }
}
