<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\UseCases\CashTransfer\ListCashTransferInteractor;
use App\UseCases\CashTransfer\LoadCashTransferAccountDropdownInteractor;
use App\UseCases\CashTransfer\Requests\CashTransferRequest;
use App\UseCases\CashTransfer\StoreCashTransferInteractor;
use App\UseCases\JournalEntry\Requests\JournalEntryRequest;
use App\UseCases\JournalEntry\StoreJournalEntryInteractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CashTransferController extends Controller
{
    public function index(ListCashTransferInteractor $listCashTransferInteractor): array
    {
        return $listCashTransferInteractor->execute(auth()->id());
    }

    public function loadCashTransferAccountDropdown(LoadCashTransferAccountDropdownInteractor $loadCashTransferAccountDropdownInteractor): JsonResponse
    {
        return response()->json($loadCashTransferAccountDropdownInteractor->execute(request('search_key'),request('posting_Account')));
    }

    public function store(CashTransferRequest $cashTransferRequest, StoreCashTransferInteractor $storeCashTransferInteractor, StoreJournalEntryInteractor $storeJournalEntryInteractor): JsonResponse
    {

        $balance = DB::table('journal_entries')
            ->selectRaw('COALESCE(SUM(debit),0) - COALESCE(SUM(credit),0) as balance')
            ->where('ledger_code', $cashTransferRequest->credit_account)
            ->value('balance');

        if ($cashTransferRequest->amount > $balance) {
            return response()->json([
                'message' => 'Insufficient balance in the credit account.',
                'available_balance' => $balance,
            ], 422);
        }

        $journalEntries = [
            [
                'user_id'        => auth()->id(),
                'ledger_code'    => $cashTransferRequest->debit_account,
                'reference_type' => JournalEntry::STATUS['CashTransfer'],
                'reference_id'   => $cashTransferRequest->description,
                'debit'          => $cashTransferRequest->amount,
                'credit'         => 0,
            ],
            [
                'user_id'        => auth()->id(),
                'ledger_code'    => $cashTransferRequest->credit_account,
                'reference_type' => JournalEntry::STATUS['CashTransfer'],
                'reference_id'   => $cashTransferRequest->description,
                'debit'          => 0,
                'credit'         => $cashTransferRequest->amount,
            ],
        ];
        $this->storeJournalEntries($storeJournalEntryInteractor, $journalEntries);

        request()->merge(['user_id' => auth()->id()]);
        $category = $storeCashTransferInteractor->execute(CashTransferRequest::validateAndCreate(request()->all()));
        return response()->json($category, 201);
    }

    public function storeJournalEntries(StoreJournalEntryInteractor $storeJournalEntryInteractor, array $entries): void
    {
        foreach ($entries as $entry) {
            $journalRequest = JournalEntryRequest::validateAndCreate($entry);
            $storeJournalEntryInteractor->execute($journalRequest);
        }
    }
}
