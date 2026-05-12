<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App\Concerns\ResolvesCurrentProject;
use App\Services\CostAnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsWebController extends Controller
{
    use ResolvesCurrentProject;

    public function __invoke(Request $request, CostAnalyticsService $analytics): Response
    {
        $project = $this->currentProject($request);
        $filters = $request->only(['date_from', 'date_to', 'tag_ids', 'material_id', 'contractor_id']);

        return Inertia::render('App/Analytics/Index', $this->sharedProjects($request, $project) + [
            'summary' => $analytics->getActualPayments($project, $filters),
            'stageCost' => $analytics->getStageCost($project, $filters),
            'byMonths' => $analytics->getCostByMonths($project, $filters),
            'byTags' => $analytics->getCostByTags($project, $filters),
            'byMaterials' => $analytics->getCostByMaterials($project, $filters),
            'byContractors' => $analytics->getCostByContractors($project, $filters),
            'filters' => $filters,
        ]);
    }
}
