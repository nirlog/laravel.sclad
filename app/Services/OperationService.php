<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\MaterialPurchase;
use App\Models\MaterialWriteOff;
use App\Models\Project;
use App\Models\ServiceEntry;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OperationService
{
    public function getOperations(Project $project, array $filters = []): LengthAwarePaginator
    {
        $operations = $this->collectOperations($project, $filters)
            ->sortByDesc('date')
            ->values();

        $perPage = (int) ($filters['per_page'] ?? 15);
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $operations->forPage($page, $perPage)->values(),
            $operations->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getRecentOperations(Project $project, int $limit = 10): Collection
    {
        return $this->collectOperations($project, [])
            ->sortByDesc('date')
            ->take($limit)
            ->values();
    }

    private function collectOperations(Project $project, array $filters): Collection
    {
        return collect()
            ->merge($this->purchases($project, $filters))
            ->merge($this->writeOffs($project, $filters))
            ->merge($this->services($project, $filters))
            ->merge($this->adjustments($project, $filters))
            ->when($filters['type'] ?? null, fn (Collection $items, string $type) => $items->where('type', $type)->values());
    }

    private function purchases(Project $project, array $filters): Collection
    {
        return $this->applyDates($project->materialPurchases()->with(['items.material.unit', 'tags']), $filters)
            ->get()
            ->map(fn (MaterialPurchase $purchase) => [
                'id' => 'purchase:'.$purchase->id,
                'type' => 'purchase',
                'type_label' => 'Покупка',
                'date' => $purchase->date?->toDateString(),
                'title' => 'Покупка материалов',
                'description' => $purchase->items->pluck('material.name')->filter()->join(', '),
                'quantity_label' => $purchase->items->map(fn ($item) => $item->quantity.' '.$item->material?->unit?->short_name)->join(', '),
                'amount' => (float) $purchase->total_amount,
                'tags' => $purchase->tags,
                'url' => route('app.purchases.show', $purchase),
                'comment' => $purchase->comment,
            ]);
    }

    private function writeOffs(Project $project, array $filters): Collection
    {
        return $this->applyDates($project->materialWriteOffs()->with(['material.unit', 'tags']), $filters)
            ->get()
            ->map(fn (MaterialWriteOff $writeOff) => [
                'id' => 'write_off:'.$writeOff->id,
                'type' => 'write_off',
                'type_label' => 'Списание',
                'date' => $writeOff->date?->toDateString(),
                'title' => $writeOff->material?->name ?? 'Списание материала',
                'description' => $writeOff->comment,
                'quantity_label' => $writeOff->quantity.' '.$writeOff->material?->unit?->short_name,
                'amount' => (float) $writeOff->total_amount,
                'tags' => $writeOff->tags,
                'url' => route('app.write-offs.show', $writeOff),
                'comment' => $writeOff->comment,
            ]);
    }

    private function services(Project $project, array $filters): Collection
    {
        return $this->applyDates($project->serviceEntries()->with(['contractor', 'tags']), $filters)
            ->when($filters['contractor_id'] ?? null, fn ($query, $id) => $query->where('contractor_id', $id))
            ->get()
            ->map(fn (ServiceEntry $entry) => [
                'id' => 'service:'.$entry->id,
                'type' => 'service',
                'type_label' => 'Услуга',
                'date' => $entry->date?->toDateString(),
                'title' => $entry->name,
                'description' => $entry->contractor?->name,
                'quantity_label' => $entry->pricing_type === 'hourly' ? $entry->hours.' ч' : ($entry->quantity ? $entry->quantity.' '.$entry->unit_name : '—'),
                'amount' => (float) $entry->total_amount,
                'tags' => $entry->tags,
                'url' => route('app.services.show', $entry),
                'comment' => $entry->comment,
            ]);
    }

    private function adjustments(Project $project, array $filters): Collection
    {
        return $this->applyDates($project->inventoryMovements()->with(['material.unit', 'tags'])->where('type', 'adjustment'), $filters)
            ->get()
            ->map(fn (InventoryMovement $movement) => [
                'id' => 'adjustment:'.$movement->id,
                'type' => 'adjustment',
                'type_label' => 'Корректировка',
                'date' => $movement->date?->toDateString(),
                'title' => $movement->material?->name ?? 'Корректировка склада',
                'description' => $movement->comment,
                'quantity_label' => $movement->quantity.' '.$movement->material?->unit?->short_name,
                'amount' => (float) ($movement->amount ?? 0),
                'tags' => $movement->tags,
                'url' => route('app.inventory.index'),
                'comment' => $movement->comment,
            ]);
    }

    private function applyDates($query, array $filters)
    {
        return $query
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date));
    }
}
