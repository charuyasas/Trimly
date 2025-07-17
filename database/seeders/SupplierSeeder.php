<?php

namespace Database\Seeders;

use App\UseCases\Supplier\SeedSuppliersInteractor;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        app(SeedSuppliersInteractor::class)->execute(20);
    }
}
