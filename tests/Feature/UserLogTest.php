<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Log; // Make sure Log model is imported
use Illuminate\Support\Facades\Hash;

class UserLogTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_login_is_logged()
    {
        $user = User::factory()->create([
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('s3cr3tPassword!'),
        ]);

        // Simulate login request
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 's3cr3tPassword!',
        ]);

        $response->assertStatus(200); // Assuming login is successful

        // Check the logs table
        $this->assertDatabaseHas('logs', [
            'user_id' => $user->id,
            'action' => 'user_logged_in',
            // 'message' can also be checked if it's consistent
            // 'ip_address' and 'user_agent' are harder to assert precisely in tests
            // unless you mock the request properties.
        ]);

        // Verify that the log message is as expected (optional, but good for detail)
        $logEntry = Log::where('user_id', $user->id)->where('action', 'user_logged_in')->first();
        $this->assertNotNull($logEntry);
        $this->assertEquals("User {$user->email} logged in.", $logEntry->message);
    }
}
