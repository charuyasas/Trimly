<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DefaultUserSeeder::class,
            EmployeeSeeder::class,
            ServicesSeeder::class,
            CustomerSeeder::class,
            MainAccountSeeder::class,
            HeadingAccountSeeder::class,
            TitleAccountSeeder::class,
            CategorySeeder::class,
            SubCategorySeeder::class,
            SupplierSeeder::class
        ]);

    }
}
