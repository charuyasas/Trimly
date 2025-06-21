<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Laravel\Sanctum\Sanctum;

class UserRoleAssignmentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;
    protected $testRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->user = $this->createUser();
        $this->testRole = Role::factory()->create(['name' => 'tester_role']);
    }

    public function test_admin_can_assign_role_to_user()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson("/api/users/{$this->user->id}/roles", [
            'role_id' => $this->testRole->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => "Role '{$this->testRole->name}' assigned to user '{$this->user->name}' successfully."]);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $this->user->id,
            'role_id' => $this->testRole->id,
        ]);
        // $this->assertTrue($this->user->roles->contains($this->testRole)); // Alternative check
    }

    public function test_admin_can_revoke_role_from_user()
    {
        Sanctum::actingAs($this->admin);
        $this->user->roles()->attach($this->testRole);
        $this->assertTrue($this->user->roles->contains($this->testRole));

        $response = $this->deleteJson("/api/users/{$this->user->id}/roles/{$this->testRole->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => "Role '{$this->testRole->name}' revoked from user '{$this->user->name}' successfully."]);

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $this->user->id,
            'role_id' => $this->testRole->id,
        ]);
        // $this->assertFalse($this->user->fresh()->roles->contains($this->testRole)); // Alternative check
    }

    public function test_admin_assigning_non_existent_role_fails()
    {
        Sanctum::actingAs($this->admin);
        $nonExistentRoleId = 9999;
        $response = $this->postJson("/api/users/{$this->user->id}/roles", ['role_id' => $nonExistentRoleId]);
        $response->assertStatus(422)->assertJsonValidationErrors('role_id'); // Expecting validation error
    }

    public function test_admin_revoking_non_assigned_role_returns_appropriate_message()
    {
        Sanctum::actingAs($this->admin);
        // Ensure user does not have testRole
        $this->user->roles()->detach($this->testRole->id);

        $response = $this->deleteJson("/api/users/{$this->user->id}/roles/{$this->testRole->id}");

        // Depending on implementation, this might be a 400, 404, or 200 with a specific message.
        // Current UserRoleController returns a 400 if role not found on user for revocation.
        $response->assertStatus(400)
            ->assertJson(['message' => "User '{$this->user->name}' does not have the role '{$this->testRole->name}'."]);
    }


    // Non-admin tests
    public function test_non_admin_cannot_assign_role()
    {
        Sanctum::actingAs($this->user); // Authenticate as the regular user

        $response = $this->postJson("/api/users/{$this->user->id}/roles", [
            'role_id' => $this->testRole->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('role_user', [
            'user_id' => $this->user->id,
            'role_id' => $this->testRole->id,
        ]);
    }

    public function test_non_admin_cannot_revoke_role()
    {
        // Assign a role first (as admin or directly)
        $this->user->roles()->attach($this->testRole);
        $this->assertTrue($this->user->roles->contains($this->testRole));

        Sanctum::actingAs($this->user); // Authenticate as the regular user

        $response = $this->deleteJson("/api/users/{$this->user->id}/roles/{$this->testRole->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('role_user', [ // Role should still be there
            'user_id' => $this->user->id,
            'role_id' => $this->testRole->id,
        ]);
    }
}
