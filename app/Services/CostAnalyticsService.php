<?php

namespace App\Services;

use App\Models\Project;

class CostAnalyticsService
{
    public function getActualPayments(Project $project, array $filters = []): array
    {
        $purchasesTotal = (float) $this->date($project->materialPurchases(), $filters)->sum('total_amount');
        $servicesTotal = (float) $this->date($project->serviceEntries(), $filters)->sum('total_amount');
        $writtenOffTotal = (float) $this->date($project->materialWriteOffs(), $filters)->sum('total_amount');
        $inventoryValue = (float) app(InventoryService::class)->getInventoryTable($project)->sum('stock_value');
        $currentMonth = now()->format('Y-m');

        $currentMonthPurchases = $project->materialPurchases->filter(fn ($purchase) => $purchase->date?->format('Y-m') === $currentMonth)->sum('total_amount');
        $currentMonthServices = $project->serviceEntries->filter(fn ($entry) => $entry->date?->format('Y-m') === $currentMonth)->sum('total_amount');

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
        return $project->tags()->orderBy('name')->get()->map(function ($tag) use ($filters) {
            $purchases = $this->date($tag->morphedByMany(\App\Models\MaterialPurchase::class, 'taggable'), $filters)->sum('total_amount');
            $services = $this->date($tag->morphedByMany(\App\Models\ServiceEntry::class, 'taggable'), $filters)->sum('total_amount');
            $writeOffs = $this->date($tag->morphedByMany(\App\Models\MaterialWriteOff::class, 'taggable'), $filters)->sum('total_amount');

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
            'purchases' => $this->sumByMonth($this->date($project->materialPurchases(), $filters)->get(), 'total_amount'),
            'services' => $this->sumByMonth($this->date($project->serviceEntries(), $filters)->get(), 'total_amount'),
            'write_offs' => $this->sumByMonth($this->date($project->materialWriteOffs(), $filters)->get(), 'total_amount'),
        ];
    }

    public function getCostByContractors(Project $project, array $filters = []): array
    {
        return $this->date($project->serviceEntries()->with('contractor'), $filters)->get()
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
        return app(InventoryService::class)->getInventoryTable($project)->map(function (array $row) {
            $material = $row['material'];

            return $row + [
                'purchased_amount' => (float) $material->inventoryMovements()->where('type', 'in')->sum('amount'),
                'written_off_amount' => (float) $material->inventoryMovements()->where('type', 'out')->sum('amount'),
            ];
        })->values()->all();
    }

    public function getStageCost(Project $project, array $filters = []): array
    {
        return [
            'materials' => (float) $this->date($project->materialWriteOffs(), $filters)->sum('total_amount'),
            'services' => (float) $this->date($project->serviceEntries(), $filters)->sum('total_amount'),
        ];
    }

    private function date($query, array $filters)
    {
        return $query
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date));
    }

    private function sumByMonth($items, string $field): array
    {
        return $items->groupBy(fn ($item) => $item->date?->format('Y-m'))->map(fn ($monthItems, $month) => [
            'month' => $month,
            'total' => (float) $monthItems->sum($field),
        ])->values()->all();
    }
}
