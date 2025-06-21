<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // For potential direct check if needed, though Eloquent is preferred

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->command->info('RoleSeeder executed.');

        $adminEmail = 'admin@example.com';
        $adminPassword = 'password'; // Keep this consistent

        // Create or find the admin user
        $adminUser = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin User',
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        if ($adminUser->wasRecentlyCreated) {
            $this->command->info("Admin user {$adminEmail} created.");
        } else {
            $this->command->info("Admin user {$adminEmail} found.");
            // Optionally ensure password and active status are updated if user existed
            // if (!Hash::check($adminPassword, $adminUser->password)) {
            //     $adminUser->password = Hash::make($adminPassword);
            // }
            // $adminUser->is_active = true;
            // if($adminUser->isDirty()) {
            //     $adminUser->save();
            //     $this->command->info("Admin user {$adminEmail} details (password/active status) updated.");
            // }
        }

        // Find the 'admin' role
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('CRITICAL: Admin role not found in roles table. Cannot assign admin privileges.');
            return; // Stop if admin role doesn't exist
        }
        $this->command->info("Admin role (ID: {$adminRole->id}) found.");

        // Check if the user already has the admin role
        // Note: roles() is the BelongsToMany relationship in User model
        if ($adminUser->roles->contains($adminRole)) {
            $this->command->info("User {$adminEmail} already has the 'admin' role.");
        } else {
            $this->command->info("Assigning 'admin' role to {$adminEmail}...");
            $adminUser->roles()->attach($adminRole->id);
            // Re-check to confirm attachment for robust logging
            $adminUser->load('roles'); // Refresh the roles relationship
            if ($adminUser->roles->contains($adminRole)) {
                $this->command->info("'admin' role successfully assigned to {$adminEmail}.");
            } else {
                $this->command->error("CRITICAL: Failed to assign 'admin' role to {$adminEmail} after attach call.");
            }
        }

        $this->command->comment("Default admin credentials: Email = {$adminEmail}, Password = {$adminPassword}");
    }
}
