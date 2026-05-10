<?php

namespace Tests\Feature;

use App\Actions\CreateMaterialPurchaseAction;
use App\Actions\CreateMaterialWriteOffAction;
use App\Actions\DeleteMaterialPurchaseAction;
use App\Actions\UpdateMaterialPurchaseAction;
use App\Models\Material;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PurchaseInventorySafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_delete_purchase_when_it_would_make_stock_negative(): void
    {
        [$project, $material] = $this->fixture();
        $purchase = $this->purchase($project, $material, 10);
        $this->writeOff($project, $material, 8);

        $this->expectException(RuntimeException::class);

        app(DeleteMaterialPurchaseAction::class)->execute($purchase);
    }

    public function test_cannot_reduce_purchase_below_already_written_off_quantity(): void
    {
        [$project, $material] = $this->fixture();
        $purchase = $this->purchase($project, $material, 10);
        $this->writeOff($project, $material, 8);

        $this->expectException(RuntimeException::class);

        app(UpdateMaterialPurchaseAction::class)->execute($purchase, $this->purchasePayload($project, $material, 5));
    }

    public function test_can_increase_purchase_quantity_and_keep_purchase_id(): void
    {
        [$project, $material] = $this->fixture();
        $purchase = $this->purchase($project, $material, 10);
        $this->writeOff($project, $material, 8);

        $updated = app(UpdateMaterialPurchaseAction::class)->execute($purchase, $this->purchasePayload($project, $material, 12));

        $this->assertSame($purchase->id, $updated->id);
        $this->assertSame(4.0, app(InventoryService::class)->getCurrentStock($project, $material));
        $this->assertEquals(1200, $updated->total_amount);
    }

    private function fixture(): array
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Дом']);
        $unit = Unit::create(['name' => 'штука', 'short_name' => 'шт']);
        $material = Material::create(['project_id' => $project->id, 'unit_id' => $unit->id, 'name' => 'Кирпич']);

        return [$project, $material];
    }

    private function purchase(Project $project, Material $material, float $quantity)
    {
        return app(CreateMaterialPurchaseAction::class)->execute($this->purchasePayload($project, $material, $quantity));
    }

    private function writeOff(Project $project, Material $material, float $quantity): void
    {
        app(CreateMaterialWriteOffAction::class)->execute([
            'project_id' => $project->id,
            'material_id' => $material->id,
            'date' => '2026-05-02',
            'quantity' => $quantity,
        ]);
    }

    private function purchasePayload(Project $project, Material $material, float $quantity): array
    {
        return [
            'project_id' => $project->id,
            'date' => '2026-05-01',
            'payment_status' => 'paid',
            'items' => [[
                'material_id' => $material->id,
                'quantity' => $quantity,
                'unit_price' => 100,
            ]],
        ];
    }
}
