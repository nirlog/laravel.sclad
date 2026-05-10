<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App\Concerns\ResolvesCurrentProject;
use App\Services\OperationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OperationsController extends Controller
{
    use ResolvesCurrentProject;

    public function __invoke(Request $request, OperationService $operations): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Operations/Index', $this->sharedProjects($request, $project) + [
            'operations' => $operations->getOperations($project, $request->all()),
            'filters' => $request->only(['date_from', 'date_to', 'type', 'tag_ids', 'material_id', 'contractor_id']),
        ]);
    }

    public function create(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Operations/Create', $this->sharedProjects($request, $project));
    }
}
