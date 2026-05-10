<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WebProjectSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_project_is_remembered_between_web_app_requests(): void
    {
        $user = User::factory()->create();
        Project::create(['user_id' => $user->id, 'name' => 'Первый дом', 'status' => 'active']);
        $secondProject = Project::create(['user_id' => $user->id, 'name' => 'Второй дом', 'status' => 'active']);

        $this->actingAs($user)
            ->get('/app?project_id='.$secondProject->id)
            ->assertOk()
            ->assertSessionHas('current_project_id', $secondProject->id);

        $this->actingAs($user)
            ->get('/app/materials')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('project.id', $secondProject->id)
            );
    }

    public function test_foreign_project_id_is_not_remembered_or_used(): void
    {
        $user = User::factory()->create();
        $ownProject = Project::create(['user_id' => $user->id, 'name' => 'Свой дом', 'status' => 'active']);
        $foreignUser = User::factory()->create();
        $foreignProject = Project::create(['user_id' => $foreignUser->id, 'name' => 'Чужой дом', 'status' => 'active']);

        $this->actingAs($user)
            ->get('/app?project_id='.$foreignProject->id)
            ->assertOk()
            ->assertSessionHas('current_project_id', $ownProject->id)
            ->assertInertia(fn (Assert $page) => $page
                ->where('project.id', $ownProject->id)
            );

        $this->assertNotSame($foreignProject->id, $this->app['session']->get('current_project_id'));
    }
}
