<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockSheet;

class StockSheetSeeder extends Seeder
{
    public function run(): void
    {
        StockSheet::factory()->count(500)->create();
    }
}
