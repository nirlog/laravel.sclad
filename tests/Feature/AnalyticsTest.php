<?php

namespace Tests\Feature;

use App\Actions\CreateMaterialPurchaseAction;
use App\Actions\CreateMaterialWriteOffAction;
use App\Actions\CreateServiceEntryAction;
use App\Models\Material;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Services\CostAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_actual_payments_and_stage_cost_do_not_double_count_materials(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Дом']);
        $unit = Unit::create(['name' => 'штука', 'short_name' => 'шт']);
        $material = Material::create(['project_id' => $project->id, 'unit_id' => $unit->id, 'name' => 'Кирпич']);

        app(CreateMaterialPurchaseAction::class)->execute([
            'project_id' => $project->id,
            'date' => '2026-05-01',
            'payment_status' => 'paid',
            'items' => [[
                'material_id' => $material->id,
                'quantity' => 10,
                'unit_price' => 100,
            ]],
        ]);

        app(CreateMaterialWriteOffAction::class)->execute([
            'project_id' => $project->id,
            'material_id' => $material->id,
            'date' => '2026-05-02',
            'quantity' => 4,
        ]);

        app(CreateServiceEntryAction::class)->execute([
            'project_id' => $project->id,
            'date' => '2026-05-03',
            'name' => 'Работа',
            'pricing_type' => 'fixed',
            'total_amount' => 500,
            'payment_status' => 'paid',
        ]);

        $analytics = app(CostAnalyticsService::class);
        $summary = $analytics->getActualPayments($project);
        $stageCost = $analytics->getStageCost($project);

        $this->assertSame(1500.0, $summary['actual_total']);
        $this->assertSame(1000.0, $summary['materials_purchased_total']);
        $this->assertSame(400.0, $summary['materials_written_off_total']);
        $this->assertSame(400.0, $stageCost['materials']);
        $this->assertSame(500.0, $stageCost['services']);
    }
}
