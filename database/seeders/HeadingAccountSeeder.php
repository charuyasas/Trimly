<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeadingAccount;

class HeadingAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Manually defined seed data (example)
        HeadingAccount::create(['heading_account' => 'Raw Materials', 'main_code' => 1]);
        HeadingAccount::create(['heading_account' => 'Share Capital', 'main_code' => 2]);
        HeadingAccount::create(['heading_account' => 'Salaries', 'main_code' => 3]);
    }
}
