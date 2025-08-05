<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SidebarLinkSeeder::class,
            DefaultUserSeeder::class,
            MainAccountSeeder::class,
            HeadingAccountSeeder::class,
            TitleAccountSeeder::class,
            PostingAccountSeeder::class,
            EmployeeSeeder::class,
            CustomerSeeder::class,
            ServicesSeeder::class,
            CategorySeeder::class,
            SubCategorySeeder::class,
            SupplierSeeder::class,
            ItemSeeder::class,
            StockSheetSeeder::class,
        ]);
    }
}
