<?php

namespace App\UseCases\Expenses;


use App\Models\PostingAccount;

class LoadExpensesAccountDropdownInteractor
{
    public function execute($search)
    {
        return PostingAccount::where('title_code', 20)
            ->Where('posting_account', 'like', "%$search%")
            ->limit(10)
            ->orderBy('posting_code', 'asc')
            ->get()
            ->map(fn($account) => [
                'label' =>  $account->posting_account . ' (' . $account->ledger_code . ')',
                'value' => $account->ledger_code
            ]);
    }
}
