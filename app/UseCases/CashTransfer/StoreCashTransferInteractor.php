<?php

namespace App\UseCases\CashTransfer;

use App\Models\CashTransfer;
use App\UseCases\CashTransfer\Requests\CashTransferRequest;

class StoreCashTransferInteractor
{
    public function execute(CashTransferRequest $cashTransferRequest)
    {
        return CashTransfer::create($cashTransferRequest->toArray());
    }
}
