<?php

namespace App\UseCases\CashTransfer;

use App\Models\CashTransfer;
use App\Models\User;

class ListCashTransferInteractor
{
    public function execute(int $userId): array
    {
        $cashTransfers = CashTransfer::with(['creditAccount', 'debitAccount', 'user'])->get();

        $mappedCashTransfers = $cashTransfers->map(function ($cashTransfer) {
            return [
                'id'                  => $cashTransfer->id,
                'description'         => $cashTransfer->description,
                'amount'              => $cashTransfer->amount,
                'user_name'           => $cashTransfer->user->name ?? null,
                'credit_account_name' => $cashTransfer->creditAccount->posting_account ?? null,
                'debit_account_name'  => $cashTransfer->debitAccount->posting_account ?? null,
                'date'                => $cashTransfer->created_at->format('Y-m-d'),
            ];
        });

        return [
            'cashTransfer' => $mappedCashTransfers,
        ];
    }
}
