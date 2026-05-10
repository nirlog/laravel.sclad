<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App\Concerns\ResolvesCurrentProject;
use App\Services\CostAnalyticsService;
use App\Services\InventoryService;
use App\Services\OperationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    use ResolvesCurrentProject;

    public function __invoke(Request $request, CostAnalyticsService $analytics, OperationService $operations, InventoryService $inventory): Response
    {
        $project = $this->currentProject($request);
        $filters = $request->only(['date_from', 'date_to', 'tag_ids']);

        return Inertia::render('App/Dashboard', $this->sharedProjects($request, $project) + [
            'summary' => $analytics->getActualPayments($project, $filters),
            'recentOperations' => $operations->getRecentOperations($project),
            'topTags' => collect($analytics->getCostByTags($project, $filters))->take(5)->values(),
            'inventoryAlerts' => $inventory->getInventoryTable($project)->filter(fn ($item) => $item['current_stock'] <= 0)->values()->take(5),
        ]);
    }
}
