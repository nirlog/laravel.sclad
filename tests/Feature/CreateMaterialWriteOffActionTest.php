<?php

namespace Tests\Feature;

use App\Actions\CreateMaterialPurchaseAction;
use App\Actions\CreateMaterialWriteOffAction;
use App\Models\Material;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class CreateMaterialWriteOffActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_create_backdated_write_off_when_stock_did_not_exist_on_write_off_date(): void
    {
        [$project, $material] = $this->projectWithMaterial();

        app(CreateMaterialPurchaseAction::class)->execute([
            'project_id' => $project->id,
            'date' => '2026-01-20',
            'payment_status' => 'paid',
            'items' => [
                ['material_id' => $material->id, 'quantity' => 10, 'unit_price' => 100],
            ],
        ]);

        $this->expectException(RuntimeException::class);

        try {
            app(CreateMaterialWriteOffAction::class)->execute([
                'project_id' => $project->id,
                'material_id' => $material->id,
                'date' => '2026-01-10',
                'quantity' => 5,
            ]);
        } finally {
            $this->assertDatabaseMissing('material_write_offs', [
                'project_id' => $project->id,
                'material_id' => $material->id,
                'date' => '2026-01-10',
            ]);

            $this->assertDatabaseMissing('inventory_movements', [
                'project_id' => $project->id,
                'material_id' => $material->id,
                'date' => '2026-01-10',
                'type' => 'out',
            ]);
        }
    }

    public function test_can_create_write_off_when_stock_exists_on_write_off_date(): void
    {
        [$project, $material] = $this->projectWithMaterial();

        app(CreateMaterialPurchaseAction::class)->execute([
            'project_id' => $project->id,
            'date' => '2026-01-10',
            'payment_status' => 'paid',
            'items' => [
                ['material_id' => $material->id, 'quantity' => 10, 'unit_price' => 100],
            ],
        ]);

        $writeOff = app(CreateMaterialWriteOffAction::class)->execute([
            'project_id' => $project->id,
            'material_id' => $material->id,
            'date' => '2026-01-20',
            'quantity' => 5,
        ]);

        $this->assertSame('100.00', $writeOff->unit_price);
        $this->assertSame('500.00', $writeOff->total_amount);
        $this->assertDatabaseHas('inventory_movements', [
            'source_type' => $writeOff::class,
            'source_id' => $writeOff->id,
            'date' => '2026-01-20',
            'type' => 'out',
            'amount' => 500,
        ]);
    }

    private function projectWithMaterial(): array
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Дом']);
        $unit = Unit::create(['name' => 'мешок', 'short_name' => 'мешок']);
        $material = Material::create(['project_id' => $project->id, 'unit_id' => $unit->id, 'name' => 'Цемент']);

        return [$project, $material];
    }
}
