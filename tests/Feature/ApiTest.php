<?php
namespace Tests\Feature;
use App\Models\{Project,User};use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class ApiTest extends TestCase{use RefreshDatabase; public function test_unauthorized_user_cannot_access_api(): void{$this->getJson('/api/projects')->assertUnauthorized();} public function test_user_only_sees_own_projects(): void{$u=User::factory()->create();$other=User::factory()->create();Project::create(['user_id'=>$u->id,'name'=>'Мой дом']);Project::create(['user_id'=>$other->id,'name'=>'Чужой дом']);$this->actingAs($u,'sanctum')->getJson('/api/projects')->assertOk()->assertJsonCount(1);}}
