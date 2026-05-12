<?php

namespace Tests\Feature;

use App\Models\Contractor;
use App\Models\Material;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiProjectScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_create_purchase_in_foreign_project(): void
    {
        [$user, $project, $material] = $this->fixture('Дом');
        [, $foreignProject] = $this->fixture('Чужой дом');

        $this->actingAs($user, 'sanctum')->postJson("/api/projects/{$foreignProject->id}/material-purchases", [
            'date' => '2026-05-10',
            'payment_status' => 'paid',
            'items' => [['material_id' => $material->id, 'quantity' => 1, 'unit_price' => 100]],
        ])->assertForbidden();
    }

    public function test_user_cannot_create_purchase_with_foreign_material(): void
    {
        [$user, $project] = $this->fixture('Дом');
        [, , $foreignMaterial] = $this->fixture('Чужой дом');

        $this->actingAs($user, 'sanctum')->postJson("/api/projects/{$project->id}/material-purchases", [
            'date' => '2026-05-10',
            'payment_status' => 'paid',
            'items' => [['material_id' => $foreignMaterial->id, 'quantity' => 1, 'unit_price' => 100]],
        ])->assertUnprocessable();
    }

    public function test_user_cannot_create_write_off_with_foreign_material(): void
    {
        [$user, $project] = $this->fixture('Дом');
        [, , $foreignMaterial] = $this->fixture('Чужой дом');

        $this->actingAs($user, 'sanctum')->postJson("/api/projects/{$project->id}/material-write-offs", [
            'date' => '2026-05-10',
            'material_id' => $foreignMaterial->id,
            'quantity' => 1,
        ])->assertUnprocessable();
    }

    public function test_user_cannot_create_service_with_foreign_contractor(): void
    {
        [$user, $project] = $this->fixture('Дом');
        [, $foreignProject] = $this->fixture('Чужой дом');
        $foreignContractor = Contractor::create(['project_id' => $foreignProject->id, 'name' => 'Чужая бригада']);

        $this->actingAs($user, 'sanctum')->postJson("/api/projects/{$project->id}/service-entries", [
            'date' => '2026-05-10',
            'name' => 'Работа',
            'contractor_id' => $foreignContractor->id,
            'pricing_type' => 'fixed',
            'total_amount' => 1000,
            'payment_status' => 'paid',
        ])->assertUnprocessable();
    }

    private function fixture(string $name): array
    {
        $unit = Unit::firstOrCreate(['short_name' => 'шт'], ['name' => 'штука']);
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => $name]);
        $material = Material::create(['project_id' => $project->id, 'unit_id' => $unit->id, 'name' => 'Материал '.$name]);

        return [$user, $project, $material];
    }
}
