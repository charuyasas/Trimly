<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainAccount;

class MainAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = ['Cost of Sale', 'Equity', 'Expenses'];

        foreach ($accounts as $account) {
            MainAccount::create(['main_account' => $account]);
        }
    }
}
