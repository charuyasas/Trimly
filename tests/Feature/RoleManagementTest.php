<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Laravel\Sanctum\Sanctum;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->user = $this->createUser();
    }

    public function test_admin_can_create_role()
    {
        Sanctum::actingAs($this->admin);
        $roleName = $this->faker->unique()->word . '_role';

        $response = $this->postJson('/api/roles', ['name' => $roleName]);

        $response->assertStatus(201)
            ->assertJson(['name' => $roleName]);
        $this->assertDatabaseHas('roles', ['name' => $roleName]);
    }

    public function test_admin_can_get_all_roles()
    {
        Sanctum::actingAs($this->admin);
        Role::factory()->count(3)->create();

        $response = $this->getJson('/api/roles');

        $response->assertStatus(200)
            ->assertJsonCount(3 + 2); // 3 created + default admin & user from RoleSeeder
    }

    public function test_admin_can_get_a_specific_role()
    {
        Sanctum::actingAs($this->admin);
        $role = Role::factory()->create(['name' => 'editor']);

        $response = $this->getJson("/api/roles/{$role->id}");
        $response->assertStatus(200)->assertJson(['name' => 'editor']);
    }


    public function test_admin_can_update_role()
    {
        Sanctum::actingAs($this->admin);
        $role = Role::factory()->create(['name' => 'old_name']);
        $newName = 'new_name_' . $this->faker->unique()->word;

        $response = $this->putJson("/api/roles/{$role->id}", ['name' => $newName]);

        $response->assertStatus(200)
            ->assertJson(['name' => $newName]);
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => $newName]);
    }

    public function test_admin_can_delete_role()
    {
        Sanctum::actingAs($this->admin);
        $role = Role::factory()->create(['name' => 'to_be_deleted']);

        $response = $this->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(204); // No content
        $this->assertDatabaseMissing('roles', ['id' => $role->id, 'name' => 'to_be_deleted']);
    }

    // Non-admin tests
    public function test_non_admin_cannot_create_role()
    {
        Sanctum::actingAs($this->user);
        $roleName = 'test_role_non_admin';

        $response = $this->postJson('/api/roles', ['name' => $roleName]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('roles', ['name' => $roleName]);
    }

    public function test_non_admin_cannot_get_all_roles()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/roles');
        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_get_a_specific_role()
    {
        Sanctum::actingAs($this->user);
        $role = Role::factory()->create();
        $response = $this->getJson("/api/roles/{$role->id}");
        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_update_role()
    {
        Sanctum::actingAs($this->user);
        $role = Role::factory()->create(['name' => 'protected_name']);
        $newName = 'attempted_update';

        $response = $this->putJson("/api/roles/{$role->id}", ['name' => $newName]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'protected_name']);
    }

    public function test_non_admin_cannot_delete_role()
    {
        Sanctum::actingAs($this->user);
        $role = Role::factory()->create(['name' => 'another_protected_role']);

        $response = $this->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('roles', ['name' => 'another_protected_role']);
    }

    public function test_role_name_must_be_unique_on_create()
    {
        Sanctum::actingAs($this->admin);
        Role::create(['name' => 'existing_role']);
        $response = $this->postJson('/api/roles', ['name' => 'existing_role']);
        $response->assertStatus(422)->assertJsonValidationErrors('name');
    }

    public function test_role_name_must_be_unique_on_update()
    {
        Sanctum::actingAs($this->admin);
        $role1 = Role::create(['name' => 'role_one']);
        $role2 = Role::create(['name' => 'role_two']);

        $response = $this->putJson("/api/roles/{$role2->id}", ['name' => 'role_one']);
        $response->assertStatus(422)->assertJsonValidationErrors('name');
    }
}
