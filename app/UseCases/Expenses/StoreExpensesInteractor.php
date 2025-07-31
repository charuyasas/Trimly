<?php

namespace App\UseCases\Expenses;

use App\Models\Expenses;
use App\UseCases\Expenses\Requests\ExpensesRequest;

class StoreExpensesInteractor
{
    public function execute(ExpensesRequest $expensesRequest)
    {
        return Expenses::create($expensesRequest->toArray());
    }
}
