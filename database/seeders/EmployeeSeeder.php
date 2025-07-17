<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\UseCases\Employee\SeedEmployeesInteractor;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        app(SeedEmployeesInteractor::class)->execute(50);
    }
}
