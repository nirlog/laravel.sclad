<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Material;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use RuntimeException;

class InventoryService
{
    public function getCurrentStock(Project $project, Material $material): float
    {
        return (float) InventoryMovement::query()
            ->whereBelongsTo($project)
            ->whereBelongsTo($material)
            ->selectRaw("COALESCE(SUM(CASE WHEN type='in' THEN quantity WHEN type='out' THEN -quantity WHEN type='adjustment' THEN quantity ELSE 0 END),0) as stock")
            ->value('stock');
    }

    public function getCurrentStockValue(Project $project, Material $material): float
    {
        return round($this->getCurrentStock($project, $material) * $this->getAverageUnitCost($project, $material), 2);
    }

    public function getAverageUnitCost(Project $project, Material $material, ?Carbon $date = null): float
    {
        $query = InventoryMovement::query()
            ->whereBelongsTo($project)
            ->whereBelongsTo($material)
            ->where('type', 'in');

        if ($date) {
            $query->whereDate('date', '<=', $date);
        }

        $row = $query->selectRaw('COALESCE(SUM(quantity),0) qty, COALESCE(SUM(amount),0) amount')->first();

        return (float) $row->qty > 0 ? round((float) $row->amount / (float) $row->qty, 2) : 0.0;
    }

    public function assertCanWriteOff(Project $project, Material $material, float $quantity): void
    {
        if ($this->getCurrentStock($project, $material) + 0.0001 < $quantity) {
            throw new RuntimeException('Недостаточно материала на складе для списания.');
        }
    }

    public function getInventoryTable(Project $project, array $filters = []): Collection
    {
        $materials = $project->materials()
            ->with('unit')
            ->when($filters['material_id'] ?? null, fn ($query, $materialId) => $query->whereKey($materialId))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', '%'.$search.'%'))
            ->get();

        return $materials
            ->map(function (Material $material) use ($project): array {
                $incoming = $material->inventoryMovements()->where('type', 'in')->sum('quantity');
                $outgoing = $material->inventoryMovements()->where('type', 'out')->sum('quantity');
                $last = $material->inventoryMovements()->latest('date')->first();
                $average = $this->getAverageUnitCost($project, $material);
                $stock = $this->getCurrentStock($project, $material);

                return [
                    'material' => $material,
                    'unit' => $material->unit?->short_name,
                    'current_stock' => $stock,
                    'average_price' => $average,
                    'stock_value' => round($stock * $average, 2),
                    'incoming_total' => (float) $incoming,
                    'written_off_total' => (float) $outgoing,
                    'last_operation' => $last?->date,
                ];
            })
            ->filter(function (array $row) use ($filters): bool {
                if (! empty($filters['only_positive']) && $row['current_stock'] <= 0) {
                    return false;
                }

                if (! empty($filters['only_zero']) && abs($row['current_stock']) > 0.0001) {
                    return false;
                }

                if (! empty($filters['only_problem']) && $row['current_stock'] >= 0) {
                    return false;
                }

                return true;
            })
            ->values();
    }
}
