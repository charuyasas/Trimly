<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PostingAccount;

class PostingAccountSeeder extends Seeder
{
    public function run(): void
    {
        PostingAccount::create(['posting_code' => 1000, 'posting_account' => 'Inventory', 'main_code' => 1, 'heading_code' => 2, 'title_code' => 6]);
    }
}
