<?php

namespace Tests\Feature;

use App\Actions\CreateMaterialPurchaseAction;
use App\Actions\CreateMaterialWriteOffAction;
use App\Actions\CreateServiceEntryAction;
use App\Actions\UpdateMaterialWriteOffAction;
use App\Actions\UpdateServiceEntryAction;
use App\Models\Material;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateActionsPreserveIdTest extends TestCase
{
    use RefreshDatabase;

    public function test_write_off_update_preserves_id(): void
    {
        [$project, $material] = $this->fixture();
        app(CreateMaterialPurchaseAction::class)->execute([
            'project_id' => $project->id,
            'date' => '2026-05-01',
            'payment_status' => 'paid',
            'items' => [['material_id' => $material->id, 'quantity' => 10, 'unit_price' => 100]],
        ]);
        $writeOff = app(CreateMaterialWriteOffAction::class)->execute([
            'project_id' => $project->id,
            'material_id' => $material->id,
            'date' => '2026-05-02',
            'quantity' => 2,
        ]);

        $updated = app(UpdateMaterialWriteOffAction::class)->execute($writeOff, [
            'project_id' => $project->id,
            'material_id' => $material->id,
            'date' => '2026-05-03',
            'quantity' => 3,
        ]);

        $this->assertSame($writeOff->id, $updated->id);
        $this->assertEquals(3, $updated->quantity);
    }

    public function test_service_update_preserves_id(): void
    {
        [$project] = $this->fixture();
        $entry = app(CreateServiceEntryAction::class)->execute([
            'project_id' => $project->id,
            'date' => '2026-05-01',
            'name' => 'Работа',
            'pricing_type' => 'fixed',
            'total_amount' => 1000,
            'payment_status' => 'paid',
        ]);

        $updated = app(UpdateServiceEntryAction::class)->execute($entry, [
            'project_id' => $project->id,
            'date' => '2026-05-02',
            'name' => 'Работа обновлена',
            'pricing_type' => 'fixed',
            'total_amount' => 1200,
            'payment_status' => 'paid',
        ]);

        $this->assertSame($entry->id, $updated->id);
        $this->assertSame('Работа обновлена', $updated->name);
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
}
