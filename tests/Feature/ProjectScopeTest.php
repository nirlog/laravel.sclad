<?php

namespace Tests\Feature;

use App\Actions\CreateMaterialPurchaseAction;
use App\Actions\CreateMaterialWriteOffAction;
use App\Actions\CreateServiceEntryAction;
use App\Models\Contractor;
use App\Models\Material;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProjectScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_cannot_use_material_from_another_project(): void
    {
        [$ownProject] = $this->projectWithMaterial('Дом');
        [, $foreignMaterial] = $this->projectWithMaterial('Чужой дом');

        $this->expectException(ValidationException::class);

        app(CreateMaterialPurchaseAction::class)->execute([
            'project_id' => $ownProject->id,
            'date' => '2026-05-10',
            'payment_status' => 'paid',
            'items' => [[
                'material_id' => $foreignMaterial->id,
                'quantity' => 1,
                'unit_price' => 100,
            ]],
        ]);
    }

    public function test_write_off_cannot_use_material_from_another_project(): void
    {
        [$ownProject] = $this->projectWithMaterial('Дом');
        [, $foreignMaterial] = $this->projectWithMaterial('Чужой дом');

        $this->expectException(ValidationException::class);

        app(CreateMaterialWriteOffAction::class)->execute([
            'project_id' => $ownProject->id,
            'material_id' => $foreignMaterial->id,
            'date' => '2026-05-10',
            'quantity' => 1,
        ]);
    }

    public function test_service_cannot_use_contractor_from_another_project(): void
    {
        [$ownProject] = $this->projectWithMaterial('Дом');
        [$foreignProject] = $this->projectWithMaterial('Чужой дом');
        $foreignContractor = Contractor::create(['project_id' => $foreignProject->id, 'name' => 'Чужая бригада']);

        $this->expectException(ValidationException::class);

        app(CreateServiceEntryAction::class)->execute([
            'project_id' => $ownProject->id,
            'contractor_id' => $foreignContractor->id,
            'date' => '2026-05-10',
            'name' => 'Работа',
            'pricing_type' => 'fixed',
            'total_amount' => 1000,
            'payment_status' => 'paid',
        ]);
    }

    private function projectWithMaterial(string $name): array
    {
        $unit = Unit::firstOrCreate(['short_name' => 'шт'], ['name' => 'штука']);
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => $name]);
        $material = Material::create(['project_id' => $project->id, 'unit_id' => $unit->id, 'name' => 'Материал '.$name]);

        return [$project, $material];
    }
}
