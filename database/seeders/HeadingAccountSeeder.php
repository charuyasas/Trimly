<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeadingAccount;

class HeadingAccountSeeder extends Seeder
{
    public function run(): void
    {
        HeadingAccount::create(['heading_code' => 2, 'heading_account' => 'Current Assets', 'main_code' => 1]);
        HeadingAccount::create(['heading_code' => 3,'heading_account' => 'Fixed Assets', 'main_code' => 1]);
        HeadingAccount::create(['heading_code' => 6,'heading_account' => 'Other Assets', 'main_code' => 1]);
        HeadingAccount::create(['heading_code' => 7,'heading_account' => 'Income', 'main_code' => 5]);
        HeadingAccount::create(['heading_code' => 8,'heading_account' => 'Expenses', 'main_code' => 4]);
        HeadingAccount::create(['heading_code' => 9,'heading_account' => 'Current Liabilities', 'main_code' => 6]);
        HeadingAccount::create(['heading_code' => 10,'heading_account' => 'Cost Of Sale', 'main_code' => 2]);
        HeadingAccount::create(['heading_code' => 11,'heading_account' => 'Non Current Liability', 'main_code' => 6]);
        HeadingAccount::create(['heading_code' => 12,'heading_account' => 'Long Term Liability', 'main_code' => 6]);
        HeadingAccount::create(['heading_code' => 13,'heading_account' => 'Intercompany Exchange', 'main_code' => 3]);
        HeadingAccount::create(['heading_code' => 15,'heading_account' => 'Opening Equity', 'main_code' => 3]);
        HeadingAccount::create(['heading_code' => 16,'heading_account' => 'Sales Revenue', 'main_code' => 5]);
        HeadingAccount::create(['heading_code' => 1000,'heading_account' => 'Long Term Asset', 'main_code' => 1]);
    }
}
