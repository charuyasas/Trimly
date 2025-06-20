<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase; // Useful for tests

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase; // RefreshDatabase will run migrations

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles are seeded if not already present, especially for admin
        $this->artisan('db:seed --class=RoleSeeder');
    }

    /**
     * Create a regular user.
     *
     * @param  array  $attributes
     * @return \App\Models\User
     */
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'password' => Hash::make('password'),
        ], $attributes));
    }

    /**
     * Create an admin user.
     *
     * @param  array  $attributes
     * @return \App\Models\User
     */
    protected function createAdmin(array $attributes = []): User
    {
        $adminRole = Role::firstWhere('name', 'admin');
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'admin']);
        }

        $admin = User::factory()->create(array_merge([
            'password' => Hash::make('password'),
        ], $attributes));
        $admin->roles()->attach($adminRole);
        return $admin;
    }

    /**
     * Create a role.
     *
     * @param  array  $attributes
     * @return \App\Models\Role
     */
    protected function createRole(array $attributes = []): Role
    {
        return Role::factory()->create($attributes); // Assuming you might create a RoleFactory later
        // Or Role::create($attributes) if no factory
    }
}
