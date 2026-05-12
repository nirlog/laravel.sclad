<?php

namespace Tests\Feature;

use App\Actions\CreateMaterialPurchaseAction;
use App\Actions\CreateMaterialWriteOffAction;
use App\Actions\UpdateMaterialWriteOffAction;
use App\Models\Material;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialWriteOffCostDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_write_off_uses_average_cost_as_of_write_off_date(): void
    {
        [$project, $material] = $this->projectWithDatedPurchases();

        $writeOff = app(CreateMaterialWriteOffAction::class)->execute([
            'project_id' => $project->id,
            'material_id' => $material->id,
            'date' => '2026-02-15',
            'quantity' => 1,
        ]);

        $this->assertSame('100.00', $writeOff->unit_price);
        $this->assertSame('100.00', $writeOff->total_amount);
        $this->assertNotSame('200.00', $writeOff->unit_price);
    }

    public function test_updating_write_off_date_recalculates_average_cost_for_new_date(): void
    {
        [$project, $material] = $this->projectWithDatedPurchases();

        $writeOff = app(CreateMaterialWriteOffAction::class)->execute([
            'project_id' => $project->id,
            'material_id' => $material->id,
            'date' => '2026-03-15',
            'quantity' => 1,
        ]);

        $this->assertSame('200.00', $writeOff->unit_price);

        $updated = app(UpdateMaterialWriteOffAction::class)->execute($writeOff, [
            'material_id' => $material->id,
            'date' => '2026-02-15',
            'quantity' => 1,
        ]);

        $this->assertSame('100.00', $updated->unit_price);
        $this->assertSame('100.00', $updated->total_amount);
    }

    private function projectWithDatedPurchases(): array
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Дом']);
        $unit = Unit::create(['name' => 'штука', 'short_name' => 'шт']);
        $material = Material::create(['project_id' => $project->id, 'unit_id' => $unit->id, 'name' => 'Материал']);

        app(CreateMaterialPurchaseAction::class)->execute([
            'project_id' => $project->id,
            'date' => '2026-02-01',
            'payment_status' => 'paid',
            'items' => [
                ['material_id' => $material->id, 'quantity' => 10, 'unit_price' => 100],
            ],
        ]);

        app(CreateMaterialPurchaseAction::class)->execute([
            'project_id' => $project->id,
            'date' => '2026-03-01',
            'payment_status' => 'paid',
            'items' => [
                ['material_id' => $material->id, 'quantity' => 10, 'unit_price' => 300],
            ],
        ]);

        return [$project, $material];
    }
}
