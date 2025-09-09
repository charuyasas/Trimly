<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PostingAccount;

class PostingAccountSeeder extends Seeder
{
    public function run(): void
    {
        PostingAccount::updateOrCreate(['posting_code' => 1000, 'posting_account' => 'Inventory', 'main_code' => 1, 'heading_code' => 2, 'title_code' => 6]);
        PostingAccount::updateOrCreate(['posting_code' => 1001, 'posting_account' => 'Cash in Hand', 'main_code' => 1, 'heading_code' => 2, 'title_code' => 3]);
        PostingAccount::updateOrCreate(['posting_code' => 1002, 'posting_account' => 'Income', 'main_code' => 4, 'heading_code' => 8, 'title_code' => 18]);
        PostingAccount::updateOrCreate(['posting_code' => 1003, 'posting_account' => 'Expenses', 'main_code' => 4, 'heading_code' => 8, 'title_code' => 10]);
        PostingAccount::updateOrCreate(['posting_code' => 1004, 'posting_account' => 'Sales Revenue', 'main_code' => 5, 'heading_code' => 16, 'title_code' => 19]);
        PostingAccount::updateOrCreate(['posting_code' => 1005, 'posting_account' => 'Salary Expenses', 'main_code' => 4, 'heading_code' => 8, 'title_code' => 9]);
        PostingAccount::updateOrCreate(['posting_code' => 9999, 'posting_account' => 'Bank', 'main_code' => 1, 'heading_code' => 2, 'title_code' => 21]);
        PostingAccount::updateOrCreate(['posting_code' => 9998, 'posting_account' => 'Proprietor', 'main_code' => 1, 'heading_code' => 2, 'title_code' => 21]);
    }
}
