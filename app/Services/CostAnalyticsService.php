<?php

namespace App\Services;

use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseItem;
use App\Models\MaterialWriteOff;
use App\Models\Project;
use App\Models\ServiceEntry;

class CostAnalyticsService
{
    public function getActualPayments(Project $project, array $filters = []): array
    {
        $purchasesTotal = $this->purchaseTotal($project, $filters);
        $servicesTotal = (float) $this->serviceQuery($project, $filters)->sum('total_amount');
        $writtenOffTotal = (float) $this->writeOffQuery($project, $filters)->sum('total_amount');
        $inventoryValue = (float) app(InventoryService::class)->getInventoryTable($project)->sum('stock_value');
        $currentMonth = now()->format('Y-m');

        $currentMonthPurchases = $this->purchaseTotal($project, ['date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->endOfMonth()->toDateString()] + $filters);
        $currentMonthServices = $this->serviceQuery($project, [])->get()->filter(fn ($entry) => $entry->date?->format('Y-m') === $currentMonth)->sum('total_amount');

        return [
            'actual_total' => $purchasesTotal + $servicesTotal,
            'materials_purchased_total' => $purchasesTotal,
            'services_total' => $servicesTotal,
            'materials_written_off_total' => $writtenOffTotal,
            'inventory_value' => $inventoryValue,
            'current_month_total' => (float) $currentMonthPurchases + (float) $currentMonthServices,
        ];
    }

    public function getCostByTags(Project $project, array $filters = []): array
    {
        return $project->tags()->orderBy('name')->get()->map(function ($tag) use ($project, $filters) {
            $tagFilter = array_merge($filters, ['tag_ids' => [$tag->id]]);
            $purchases = $this->purchaseTotal($project, $tagFilter);
            $services = $this->serviceQuery($project, $tagFilter)->sum('total_amount');
            $writeOffs = $this->writeOffQuery($project, $tagFilter)->sum('total_amount');

            return [
                'id' => $tag->id,
                'name' => $tag->name,
                'actual_total' => (float) $purchases + (float) $services,
                'stage_cost_total' => (float) $writeOffs + (float) $services,
            ];
        })->sortByDesc('actual_total')->values()->all();
    }

    public function getCostByMonths(Project $project, array $filters = []): array
    {
        return [
            'purchases' => $this->purchaseMonths($project, $filters),
            'services' => $this->sumByMonth($this->serviceQuery($project, $filters)->get(), 'total_amount'),
            'write_offs' => $this->sumByMonth($this->writeOffQuery($project, $filters)->get(), 'total_amount'),
        ];
    }

    public function getCostByContractors(Project $project, array $filters = []): array
    {
        return $this->serviceQuery($project, $filters)->with('contractor')->get()
            ->groupBy('contractor_id')
            ->map(fn ($entries) => [
                'contractor' => $entries->first()->contractor?->name ?? 'Без исполнителя',
                'entries_count' => $entries->count(),
                'hours_total' => (float) $entries->sum('hours'),
                'total_amount' => (float) $entries->sum('total_amount'),
                'paid_amount' => (float) $entries->sum('paid_amount'),
                'debt' => (float) $entries->sum('total_amount') - (float) $entries->sum('paid_amount'),
            ])->values()->all();
    }

    public function getCostByMaterials(Project $project, array $filters = []): array
    {
        return app(InventoryService::class)->getInventoryTable($project, $filters)->map(function (array $row) use ($project, $filters) {
            $material = $row['material'];
            $materialFilters = array_merge($filters, ['material_id' => $material->id]);

            return $row + [
                'purchased_amount' => (float) $this->purchaseItemsAmount($project, $material->id, $filters),
                'written_off_amount' => (float) $this->writeOffQuery($project, $materialFilters)->sum('total_amount'),
            ];
        })->values()->all();
    }

    public function getStageCost(Project $project, array $filters = []): array
    {
        return [
            'materials' => (float) $this->writeOffQuery($project, $filters)->sum('total_amount'),
            'services' => (float) $this->serviceQuery($project, $filters)->sum('total_amount'),
        ];
    }


    private function purchaseTotal(Project $project, array $filters): float
    {
        if (! empty($filters['material_id'])) {
            return $this->purchaseItemsAmount($project, (int) $filters['material_id'], $filters);
        }

        return (float) $this->purchaseQuery($project, $filters)->sum('total_amount');
    }

    private function purchaseMonths(Project $project, array $filters): array
    {
        if (empty($filters['material_id'])) {
            return $this->sumByMonth($this->purchaseQuery($project, $filters)->get(), 'total_amount');
        }

        return $this->purchaseItemsQuery($project, (int) $filters['material_id'], $filters)
            ->with('purchase')
            ->get()
            ->groupBy(fn (MaterialPurchaseItem $item) => $item->purchase?->date?->format('Y-m'))
            ->map(fn ($items, $month) => [
                'month' => $month,
                'total' => (float) $items->sum('total_price'),
            ])
            ->values()
            ->all();
    }

    private function purchaseItemsAmount(Project $project, int $materialId, array $filters): float
    {
        return (float) $this->purchaseItemsQuery($project, $materialId, $filters)->sum('total_price');
    }

    private function purchaseItemsQuery(Project $project, int $materialId, array $filters)
    {
        return MaterialPurchaseItem::query()
            ->where('material_id', $materialId)
            ->whereHas('purchase', function ($query) use ($project, $filters): void {
                $this->applyCommonFilters($query->where('project_id', $project->id), $filters)
                    ->when($filters['payment_status'] ?? null, fn ($query, $status) => $query->where('payment_status', $status));
            });
    }

    private function purchaseQuery(Project $project, array $filters)
    {
        return $this->applyCommonFilters($project->materialPurchases(), $filters)
            ->when($filters['payment_status'] ?? null, fn ($query, $status) => $query->where('payment_status', $status))
            ->when($filters['material_id'] ?? null, fn ($query, $materialId) => $query->whereHas('items', fn ($itemQuery) => $itemQuery->where('material_id', $materialId)));
    }

    private function writeOffQuery(Project $project, array $filters)
    {
        return $this->applyCommonFilters($project->materialWriteOffs(), $filters)
            ->when($filters['material_id'] ?? null, fn ($query, $materialId) => $query->where('material_id', $materialId));
    }

    private function serviceQuery(Project $project, array $filters)
    {
        return $this->applyCommonFilters($project->serviceEntries(), $filters)
            ->when($filters['payment_status'] ?? null, fn ($query, $status) => $query->where('payment_status', $status))
            ->when($filters['contractor_id'] ?? null, fn ($query, $contractorId) => $query->where('contractor_id', $contractorId));
    }

    private function applyCommonFilters($query, array $filters)
    {
        return $query
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date))
            ->when($filters['tag_ids'] ?? null, fn ($query, $tagIds) => $query->whereHas('tags', fn ($tagQuery) => $tagQuery->whereIn('tags.id', (array) $tagIds)));
    }

    private function sumByMonth($items, string $field): array
    {
        return $items->groupBy(fn ($item) => $item->date?->format('Y-m'))->map(fn ($monthItems, $month) => [
            'month' => $month,
            'total' => (float) $monthItems->sum($field),
        ])->values()->all();
    }
}
