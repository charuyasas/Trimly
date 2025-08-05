<?php

namespace Database\Factories;

use App\Models\StockSheet;
use App\Models\Item;
use App\Models\PostingAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StockSheetFactory extends Factory
{
    protected $model = StockSheet::class;

    public function definition(): array
    {
        $referenceType = $this->faker->randomElement([
            'GRN',
            'Sale',
            'Employee Issue',
            'Employee Consumption'
        ]);

        // Default ledger for GRN
        $ledgerCode = '1-2-6-1000';

        // If not GRN, get an employee ledger code from PostingAccount
        if ($referenceType !== 'GRN') {
            $employeeLedgerCodes = PostingAccount::where('title_code', 9)
                ->pluck('ledger_code')
                ->toArray();

            $ledgerCode = $this->faker->randomElement($employeeLedgerCodes ?: [$ledgerCode]);
        }

        return [
            'id' => Str::uuid(),
            'item_code' => Item::inRandomOrder()->first()->id ?? Item::factory(),
            'ledger_code' => $ledgerCode,
            'description' => $this->faker->sentence(3),
            'reference_type' => $referenceType,
            'reference_id' => Str::uuid(),
            'credit' => $this->faker->randomFloat(2, 0, 1000),
            'debit' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
