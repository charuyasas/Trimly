<?php

namespace App\UseCases\Expenses;

use App\Constance\AccountsLedgerCodes;
use App\Models\JournalEntry;

class GetCashBalanceInteractor
{
    public function execute(): float
    {
        return JournalEntry::where('ledger_code', AccountsLedgerCodes::LEDGER_CODES['Cash in Hand'])
            ->selectRaw('SUM(debit) - SUM(credit) as cash_balance')
            ->value('cash_balance') ?? 0;
    }
}
