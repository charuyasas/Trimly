<?php

namespace App\UseCases\CashTransfer;

use App\Constance\AccountsLedgerCodes;
use App\Models\PostingAccount;
use Illuminate\Support\Facades\DB;

class LoadCashTransferAccountDropdownInteractor
{
    public function execute($search, $posting_Account)
    {

        $allowedLedgerCodes = [AccountsLedgerCodes::LEDGER_CODES['Cash in Hand'], AccountsLedgerCodes::LEDGER_CODES['Banks'], AccountsLedgerCodes::LEDGER_CODES['Proprietor']];
        return PostingAccount::whereIn('ledger_code', $allowedLedgerCodes)
            ->where('posting_account', 'like', "%$search%")
            ->when(!empty($posting_Account), function ($query) use ($posting_Account) {
                $query->where('ledger_code', '!=', $posting_Account);
            })
            ->limit(10)
            ->orderBy('posting_code', 'asc')
            ->get()
            ->map(function ($account) {
                $balance = DB::table('journal_entries')
                    ->selectRaw('COALESCE(SUM(debit),0) - COALESCE(SUM(credit),0) as balance')
                    ->where('ledger_code', $account->ledger_code)
                    ->value('balance');

                return [
                    'label'   => $account->posting_account . ' (' . $account->ledger_code . ')',
                    'value'   => $account->ledger_code,
                    'balance' => $balance,
                ];
            });
    }
}
