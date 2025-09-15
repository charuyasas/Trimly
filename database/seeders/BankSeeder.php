<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        Bank::updateOrCreate(['bank_code' => 7010, 'bank_name' => 'Bank of Ceylon']);
        Bank::updateOrCreate(['bank_code' => 7056, 'bank_name' => 'Commercial Bank PLC']);
        Bank::updateOrCreate(['bank_code' => 7454, 'bank_name' => 'DFCC Bank PLC']);
        Bank::updateOrCreate(['bank_code' => 7083, 'bank_name' => 'Hatton National Bank PLC']);
        Bank::updateOrCreate(['bank_code' => 7135, 'bank_name' => 'Peoples Bank']);
    }
}
