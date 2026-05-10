<?php
namespace App\Services;
use App\Models\{InventoryMovement,Material,Project};use Carbon\Carbon;use Illuminate\Support\Collection;use RuntimeException;
class InventoryService
{
    public function getCurrentStock(Project $project, Material $material): float {return (float) InventoryMovement::query()->whereBelongsTo($project)->whereBelongsTo($material)->selectRaw("COALESCE(SUM(CASE WHEN type='in' THEN quantity WHEN type='out' THEN -quantity WHEN type='adjustment' THEN quantity ELSE 0 END),0) as stock")->value('stock');}
    public function getCurrentStockValue(Project $project, Material $material): float {return round($this->getCurrentStock($project,$material)*$this->getAverageUnitCost($project,$material),2);}
    public function getAverageUnitCost(Project $project, Material $material, ?Carbon $date=null): float {$q=InventoryMovement::query()->whereBelongsTo($project)->whereBelongsTo($material)->where('type','in'); if($date){$q->whereDate('date','<=',$date);} $r=$q->selectRaw('COALESCE(SUM(quantity),0) qty, COALESCE(SUM(amount),0) amount')->first(); return (float)$r->qty>0?round((float)$r->amount/(float)$r->qty,2):0.0;}
    public function assertCanWriteOff(Project $project, Material $material, float $quantity): void {if($this->getCurrentStock($project,$material)+0.0001 < $quantity){throw new RuntimeException('Недостаточно материала на складе для списания.');}}
    public function getInventoryTable(Project $project, array $filters=[]): Collection {return $project->materials()->with('unit')->get()->map(function(Material $m) use($project){$in=$m->inventoryMovements()->where('type','in')->sum('quantity');$out=$m->inventoryMovements()->where('type','out')->sum('quantity');$last=$m->inventoryMovements()->latest('date')->first();$avg=$this->getAverageUnitCost($project,$m);$stock=$this->getCurrentStock($project,$m);return ['material'=>$m,'unit'=>$m->unit?->short_name,'current_stock'=>$stock,'average_price'=>$avg,'stock_value'=>round($stock*$avg,2),'incoming_total'=>(float)$in,'written_off_total'=>(float)$out,'last_operation'=>$last?->date];});}
}
