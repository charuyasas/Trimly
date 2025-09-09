<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TitleAccount;

class TitleAccountSeeder extends Seeder
{
    public function run(): void
    {
        TitleAccount::updateOrCreate(['title_code' => 2], ['title_account' => 'Cheques In Hand', 'main_code' => 1, 'heading_code' => 2]);
        TitleAccount::updateOrCreate(['title_code' => 3], ['title_account' => 'Cash in Hand', 'main_code' => 1, 'heading_code' => 2]);
        TitleAccount::updateOrCreate(['title_code' => 4], ['title_account' => 'Petty Cash', 'main_code' => 1, 'heading_code' => 2]);
        TitleAccount::updateOrCreate(['title_code' => 5], ['title_account' => 'Trade Debtors', 'main_code' => 1, 'heading_code' => 2]);
        TitleAccount::updateOrCreate(['title_code' => 6], ['title_account' => 'Inventory', 'main_code' => 1, 'heading_code' => 2]);
        TitleAccount::updateOrCreate(['title_code' => 7], ['title_account' => 'Sales Income', 'main_code' => 5, 'heading_code' => 7]);
        TitleAccount::updateOrCreate(['title_code' => 9], ['title_account' => 'Salary Expenses', 'main_code' => 4, 'heading_code' => 8]);
        TitleAccount::updateOrCreate(['title_code' => 10], ['title_account' => 'Stationary Expenses', 'main_code' => 4, 'heading_code' => 8]);
        TitleAccount::updateOrCreate(['title_code' => 11], ['title_account' => 'Supplier Accounts', 'main_code' => 6, 'heading_code' => 9]);
        TitleAccount::updateOrCreate(['title_code' => 12], ['title_account' => 'Cost Of Sale', 'main_code' => 2, 'heading_code' => 10]);
        TitleAccount::updateOrCreate(['title_code' => 13], ['title_account' => 'Damage Items', 'main_code' => 4, 'heading_code' => 8]);
        TitleAccount::updateOrCreate(['title_code' => 14], ['title_account' => 'Dealer Commissions', 'main_code' => 6, 'heading_code' => 9]);
        TitleAccount::updateOrCreate(['title_code' => 16], ['title_account' => 'Long Term Loan', 'main_code' => 6, 'heading_code' => 11]);
        TitleAccount::updateOrCreate(['title_code' => 17], ['title_account' => 'Tax Payable', 'main_code' => 6, 'heading_code' => 9]);
        TitleAccount::updateOrCreate(['title_code' => 18], ['title_account' => 'Cost Of Sale', 'main_code' => 4, 'heading_code' => 8]);
        TitleAccount::updateOrCreate(['title_code' => 19], ['title_account' => 'Sales Revenue', 'main_code' => 5, 'heading_code' => 16]);
        TitleAccount::updateOrCreate(['title_code' => 20], ['title_account' => 'Administrative Expenses', 'main_code' => 4, 'heading_code' => 8]);
        TitleAccount::updateOrCreate(['title_code' => 21], ['title_account' => 'Bank', 'main_code' => 1, 'heading_code' => 2]);
    }
}
