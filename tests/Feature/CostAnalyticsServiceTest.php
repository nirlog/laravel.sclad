<?php

namespace Tests\Feature;

use App\Actions\CreateServiceEntryAction;
use App\Models\Contractor;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use App\Services\CostAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CostAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_month_total_applies_tag_filters_to_services(): void
    {
        $project = $this->project();
        $foundation = Tag::create(['project_id' => $project->id, 'name' => 'фундамент', 'slug' => 'foundation']);
        $roof = Tag::create(['project_id' => $project->id, 'name' => 'кровля', 'slug' => 'roof']);

        $this->service($project, 'Фундамент', 1000, ['tag_ids' => [$foundation->id]]);
        $this->service($project, 'Кровля', 5000, ['tag_ids' => [$roof->id]]);

        $summary = app(CostAnalyticsService::class)->getActualPayments($project, [
            'tag_ids' => [$foundation->id],
        ]);

        $this->assertSame(1000.0, $summary['services_total']);
        $this->assertSame(1000.0, $summary['actual_total']);
        $this->assertSame(1000.0, $summary['current_month_total']);
    }

    public function test_current_month_total_applies_contractor_filters_to_services(): void
    {
        $project = $this->project();
        $contractorA = Contractor::create(['project_id' => $project->id, 'name' => 'Бригада A']);
        $contractorB = Contractor::create(['project_id' => $project->id, 'name' => 'Бригада B']);

        $this->service($project, 'Работа A', 1500, ['contractor_id' => $contractorA->id]);
        $this->service($project, 'Работа B', 3500, ['contractor_id' => $contractorB->id]);

        $summary = app(CostAnalyticsService::class)->getActualPayments($project, [
            'contractor_id' => $contractorA->id,
        ]);

        $this->assertSame(1500.0, $summary['services_total']);
        $this->assertSame(1500.0, $summary['actual_total']);
        $this->assertSame(1500.0, $summary['current_month_total']);
    }

    public function test_current_month_total_applies_payment_status_filters_to_services(): void
    {
        $project = $this->project();

        $this->service($project, 'Оплаченная работа', 2000, ['payment_status' => 'paid']);
        $this->service($project, 'Неоплаченная работа', 4000, ['payment_status' => 'unpaid']);

        $summary = app(CostAnalyticsService::class)->getActualPayments($project, [
            'payment_status' => 'paid',
        ]);

        $this->assertSame(2000.0, $summary['services_total']);
        $this->assertSame(2000.0, $summary['actual_total']);
        $this->assertSame(2000.0, $summary['current_month_total']);
    }

    private function project(): Project
    {
        $user = User::factory()->create();

        return Project::create(['user_id' => $user->id, 'name' => 'Дом']);
    }

    private function service(Project $project, string $name, float $amount, array $overrides = []): void
    {
        app(CreateServiceEntryAction::class)->execute(array_merge([
            'project_id' => $project->id,
            'date' => now()->startOfMonth()->addDay()->toDateString(),
            'name' => $name,
            'pricing_type' => 'fixed',
            'total_amount' => $amount,
            'payment_status' => 'paid',
        ], $overrides));
    }
}
