<?php

namespace App\Http\Controllers\API;

use App\Constance\AccountsLedgerCodes;
use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\UseCases\Expenses\GetCashBalanceInteractor;
use App\UseCases\Expenses\ListExpensesInteractor;
use App\UseCases\Expenses\LoadExpensesAccountDropdownInteractor;
use App\UseCases\Expenses\Requests\ExpensesRequest;
use App\UseCases\Expenses\StoreExpensesInteractor;
use App\UseCases\JournalEntry\Requests\JournalEntryRequest;
use App\UseCases\JournalEntry\StoreJournalEntryInteractor;
use App\UseCases\UserShift\Requests\ShiftDetailsRequest;
use Illuminate\Http\JsonResponse;

class ExpensesController extends Controller
{
    public function index(ListExpensesInteractor $listExpensesInteractor): array
    {
        return $listExpensesInteractor->execute(auth()->id());
    }

    public function store(ExpensesRequest $expensesRequest, StoreExpensesInteractor $storeExpensesInteractor, StoreJournalEntryInteractor $storeJournalEntryInteractor): JsonResponse
    {
        $journalEntries = [
            [
                'user_id'        => auth()->id(),
                'ledger_code'    => $expensesRequest->debit_account,
                'reference_type' => JournalEntry::STATUS['Expenses'],
                'reference_id'   => $expensesRequest->description,
                'debit'          => $expensesRequest->amount,
                'credit'         => 0,
            ],
            [
                'user_id'        => auth()->id(),
                'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['Cash in Hand'],
                'reference_type' => JournalEntry::STATUS['Expenses'],
                'reference_id'   => $expensesRequest->description,
                'debit'          => 0,
                'credit'         => $expensesRequest->amount,
            ],
        ];
        $this->storeJournalEntries($storeJournalEntryInteractor, $journalEntries);

        request()->merge(['user_id' => auth()->id()]);
        $category = $storeExpensesInteractor->execute(ExpensesRequest::validateAndCreate(request()->all()));
        return response()->json($category, 201);
    }

    public function loadExpensesAccountDropdown(LoadExpensesAccountDropdownInteractor $loadExpensesAccountDropdownInteractor): JsonResponse
    {
        return response()->json($loadExpensesAccountDropdownInteractor->execute(request('search_key')));
    }

    public function getCashBalance(GetCashBalanceInteractor $getCashBalanceInteractor): JsonResponse
    {
        return response()->json($getCashBalanceInteractor->execute());
    }

    public function storeJournalEntries(StoreJournalEntryInteractor $storeJournalEntryInteractor, array $entries): void
    {
        foreach ($entries as $entry) {
            $journalRequest = JournalEntryRequest::validateAndCreate($entry);
            $storeJournalEntryInteractor->execute($journalRequest);
        }
    }

}
