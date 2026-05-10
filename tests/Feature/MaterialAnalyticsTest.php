<?php

namespace Tests\Feature;

use App\Actions\CreateMaterialPurchaseAction;
use App\Models\Material;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Services\CostAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_material_analytics_uses_purchase_item_totals_not_header_total(): void
    {
        [$project, $materialA, $materialB] = $this->mixedPurchaseFixture();

        $rows = collect(app(CostAnalyticsService::class)->getCostByMaterials($project));
        $rowA = $rows->first(fn (array $row) => $row['material']->id === $materialA->id);
        $rowB = $rows->first(fn (array $row) => $row['material']->id === $materialB->id);

        $this->assertSame(10.0, $rowA['purchased_amount']);
        $this->assertSame(20.0, $rowB['purchased_amount']);
        $this->assertNotSame(30.0, $rowA['purchased_amount']);
        $this->assertNotSame(30.0, $rowB['purchased_amount']);
    }

    public function test_material_analytics_material_filter_returns_requested_material_amount(): void
    {
        [$project, $materialA, $materialB] = $this->mixedPurchaseFixture();

        $rows = app(CostAnalyticsService::class)->getCostByMaterials($project, ['material_id' => $materialB->id]);

        $this->assertCount(1, $rows);
        $this->assertSame($materialB->id, $rows[0]['material']->id);
        $this->assertSame(20.0, $rows[0]['purchased_amount']);
    }

    private function mixedPurchaseFixture(): array
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Дом']);
        $unit = Unit::create(['name' => 'штука', 'short_name' => 'шт']);
        $materialA = Material::create(['project_id' => $project->id, 'unit_id' => $unit->id, 'name' => 'Материал A']);
        $materialB = Material::create(['project_id' => $project->id, 'unit_id' => $unit->id, 'name' => 'Материал B']);

        app(CreateMaterialPurchaseAction::class)->execute([
            'project_id' => $project->id,
            'date' => '2026-05-10',
            'payment_status' => 'paid',
            'items' => [
                ['material_id' => $materialA->id, 'quantity' => 1, 'unit_price' => 10],
                ['material_id' => $materialB->id, 'quantity' => 1, 'unit_price' => 20],
            ],
        ]);

        return [$project, $materialA, $materialB];
    }
}
