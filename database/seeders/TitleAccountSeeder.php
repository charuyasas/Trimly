<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TitleAccount;

class TitleAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Example manual data
        TitleAccount::create([
            'title_account' => 'Imported Goods',
            'main_code' => 1,
            'heading_code' => 1
        ]);

        TitleAccount::create([
            'title_account' => 'Ordinary Shares',
            'main_code' => 2,
            'heading_code' => 2
        ]);

        TitleAccount::create([
            'title_account' => 'Employee Salaries',
            'main_code' => 3,
            'heading_code' => 3
        ]);
    }
}
