<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainAccount;

class MainAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = ['Asset', 'Cost Of Sale', 'Equity', 'Expenses', 'Income', 'Liability', 'Other Expenses', 'Other Income'];

        foreach ($accounts as $account) {
            MainAccount::create(['main_account' => $account]);
        }
    }
}
