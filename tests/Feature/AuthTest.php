<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase; // Already in TestCase, but good to be explicit for test file context
    use WithFaker;

    public function test_user_can_register()
    {
        $password = 'password123';
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'User registered successfully.']);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'],
        ]);
    }

    public function test_user_registration_fails_with_validation_errors()
    {
        $response = $this->postJson('/api/register', [
            'name' => '', // Invalid: name is required
            'email' => 'not-an-email', // Invalid: email format
            'password' => 'short', // Invalid: min 8 chars
            'password_confirmation' => 'different', // Invalid: doesn't match
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = $this->createUser(['email' => 'login@example.com', 'password' => Hash::make('password123')]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type']);

        // Check if login is logged
        $this->assertDatabaseHas('logs', [
            'user_id' => $user->id,
            'action' => 'user_logged_in',
        ]);
    }

    public function test_user_login_fails_with_invalid_credentials()
    {
        $this->createUser(['email' => 'wrongpass@example.com']);

        $response = $this->postJson('/api/login', [
            'email' => 'wrongpass@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401) // Or 422 if your validation returns that for bad creds
            ->assertJson(['message' => 'Invalid login details']);
    }

    public function test_inactive_user_cannot_login()
    {
        $this->createUser(['email' => 'inactive@example.com', 'is_active' => false]);

        $response = $this->postJson('/api/login', [
            'email' => 'inactive@example.com',
            'password' => 'password', // Default password from createUser helper
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Your account is inactive. Please contact support.']);
    }

    public function test_blocked_user_cannot_login()
    {
        $this->createUser(['email' => 'blocked@example.com', 'is_blocked' => true]);

        $response = $this->postJson('/api/login', [
            'email' => 'blocked@example.com',
            'password' => 'password', // Default password from createUser helper
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Your account has been blocked. Please contact an administrator.']);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200) // Or 204 if you return no content
            ->assertJson(['message' => 'Logged out successfully.']);

        // Ensure token is deleted (more complex to test directly without more involved token checks)
        // For this test, checking the response is usually sufficient for feature level.
    }

    public function test_user_can_get_their_own_data()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ])
            ->assertJsonStructure(['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at', 'roles', 'is_active', 'is_blocked']);
    }
}
