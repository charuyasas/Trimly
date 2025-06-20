<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class UserStatusTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $userToManage; // The user whose status will be changed

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->userToManage = $this->createUser();
    }

    // Activate/Deactivate Tests
    public function test_admin_can_activate_user()
    {
        Sanctum::actingAs($this->admin);
        $this->userToManage->update(['is_active' => false]); // Ensure user is initially inactive

        $response = $this->patchJson("/api/users/{$this->userToManage->id}/activate");

        $response->assertStatus(200)
            ->assertJson(['message' => "User '{$this->userToManage->name}' activated successfully."]);
        $this->assertTrue($this->userToManage->fresh()->is_active);
    }

    public function test_admin_can_deactivate_user()
    {
        Sanctum::actingAs($this->admin);
        $this->userToManage->update(['is_active' => true]); // Ensure user is initially active

        $response = $this->patchJson("/api/users/{$this->userToManage->id}/deactivate");

        $response->assertStatus(200)
            ->assertJson(['message' => "User '{$this->userToManage->name}' deactivated successfully."]);
        $this->assertFalse($this->userToManage->fresh()->is_active);
    }

    // Block/Unblock Tests
    public function test_admin_can_block_user()
    {
        Sanctum::actingAs($this->admin);
        $this->userToManage->update(['is_blocked' => false]); // Ensure user is initially unblocked

        $response = $this->patchJson("/api/users/{$this->userToManage->id}/block");

        $response->assertStatus(200)
            ->assertJson(['message' => "User '{$this->userToManage->name}' blocked successfully."]);
        $this->assertTrue($this->userToManage->fresh()->is_blocked);
    }

    public function test_admin_can_unblock_user()
    {
        Sanctum::actingAs($this->admin);
        $this->userToManage->update(['is_blocked' => true]); // Ensure user is initially blocked

        $response = $this->patchJson("/api/users/{$this->userToManage->id}/unblock");

        $response->assertStatus(200)
            ->assertJson(['message' => "User '{$this->userToManage->name}' unblocked successfully."]);
        $this->assertFalse($this->userToManage->fresh()->is_blocked);
    }

    // Non-admin tests for status changes
    public function test_non_admin_cannot_activate_user()
    {
        $regularUser = $this->createUser(); // Create another user to act as non-admin
        Sanctum::actingAs($regularUser);
        $this->userToManage->update(['is_active' => false]);

        $response = $this->patchJson("/api/users/{$this->userToManage->id}/activate");
        $response->assertStatus(403);
        $this->assertFalse($this->userToManage->fresh()->is_active);
    }

    public function test_non_admin_cannot_deactivate_user()
    {
        $regularUser = $this->createUser();
        Sanctum::actingAs($regularUser);
        $this->userToManage->update(['is_active' => true]);

        $response = $this->patchJson("/api/users/{$this->userToManage->id}/deactivate");
        $response->assertStatus(403);
        $this->assertTrue($this->userToManage->fresh()->is_active);
    }

    public function test_non_admin_cannot_block_user()
    {
        $regularUser = $this->createUser();
        Sanctum::actingAs($regularUser);
        $this->userToManage->update(['is_blocked' => false]);

        $response = $this->patchJson("/api/users/{$this->userToManage->id}/block");
        $response->assertStatus(403);
        $this->assertFalse($this->userToManage->fresh()->is_blocked);
    }

    public function test_non_admin_cannot_unblock_user()
    {
        $regularUser = $this->createUser();
        Sanctum::actingAs($regularUser);
        $this->userToManage->update(['is_blocked' => true]);

        $response = $this->patchJson("/api/users/{$this->userToManage->id}/unblock");
        $response->assertStatus(403);
        $this->assertTrue($this->userToManage->fresh()->is_blocked);
    }
}
