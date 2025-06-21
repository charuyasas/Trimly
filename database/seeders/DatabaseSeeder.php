<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Ensure this is imported
use App\Models\Role; // Ensure this is imported
use Illuminate\Support\Facades\Hash; // Ensure this is imported
// use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Not strictly needed if not using the trait

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class); // Ensures roles are created first

        // Create a default admin user
        $adminRole = Role::where('name', 'admin')->first();

        if ($adminRole) {
            $adminUser = User::firstOrCreate(
                ['email' => 'admin@example.com'], // Unique key to find existing user
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password'), // Default password: password
                    'email_verified_at' => now(),
                    'is_active' => true,
                    // is_blocked defaults to false
                ]
            );

            // Attach the admin role to the user
            // Using syncWithoutDetaching to avoid issues if already attached or if other roles exist
            $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);

            // Use $this->command->info for console output if running via artisan db:seed
            if ($this->command) {
                $this->command->info('Default admin user created/ensured: admin@example.com / password');
            }

        } else {
            if ($this->command) {
                $this->command->error('Admin role not found. Could not create default admin user.');
            }
        }

        // Example for creating regular users (optional, can be uncommented if needed)
        // $userRole = Role::where('name', 'user')->first();
        // if ($userRole) {
        //     User::factory(5)->create()->each(function ($user) use ($userRole) {
        //         $user->roles()->attach($userRole->id);
        //     });
        //     if ($this->command) {
        //         $this->command->info('Created 5 regular users with "user" role.');
        //     }
        // }
    }
}
