<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\UseCases\Grn\ListGrnInteractor;
use App\UseCases\Grn\Requests\GrnRequest;
use App\UseCases\Grn\StoreGrnInteractor;
use App\UseCases\Grn\LoadGrnDropdownInteractor;
use App\UseCases\Grn\GetGrnDetailsInteractor;
use App\UseCases\Grn\FinalizeGrnInteractor;
use App\UseCases\JournalEntry\Requests\JournalEntryRequest;
use App\UseCases\JournalEntry\StoreJournalEntryInteractor;
use App\UseCases\StockSheet\Requests\StockSheetRequest;
use App\UseCases\StockSheet\StoreStockSheetInteractor;
use Illuminate\Http\JsonResponse;

class GrnController extends Controller
{

    public function index(ListGrnInteractor $listGrnInteractor): \Illuminate\Database\Eloquent\Collection
    {
        return $listGrnInteractor->execute();
    }
    public function store(StoreGrnInteractor $storeGrnInteractor): JsonResponse
    {
        $validated = GrnRequest::validateAndCreate(request());
        $response = $storeGrnInteractor->execute($validated);

        return response()->json($response['response'], $response['status']);
    }

    public function loadGrnDropdown(LoadGrnDropdownInteractor $interactor): JsonResponse
    {
        return response()->json($interactor->execute(request('search_key')));
    }

    public function getGrnDetails($id, GetGrnDetailsInteractor $interactor): JsonResponse
    {
        return response()->json(['grn' => $interactor->execute($id)]);
    }

    public function finalize($id, GrnRequest $request, FinalizeGrnInteractor $interactor, StoreStockSheetInteractor $storeStockSheetInteractor, StoreJournalEntryInteractor $storeJournalEntryInteractor): JsonResponse
    {
        $stockdebitEntryData = collect($request->items)->map(function ($item) {
            return [
                'item_code'     => $item->item_id ?? '',
                'ledger_code'   => '2-10-12-1000',
                'description'   => 'GRN - ' . ($item->item_name ?? ''),
                'debit'         => $item->qty ?? 0,
            ];
        })->toArray();
        $this->grnItemsToStockTable($storeStockSheetInteractor, $stockdebitEntryData);

        $grnNumber = $request->grn_number;
        $grandTotal = $request->grand_total;

        $journalEntries = [
            [
                'ledger_code'    => '2-10-12-1000',
                'reference_type' => JournalEntry::STATUS['GRN'],
                'reference_id'   => 'GRN - ' . $grnNumber,
                'debit'          => $grandTotal,
                'credit'         => 0,
            ],
            [
                'ledger_code'    => $request->supplier_ledger_code,
                'reference_type' => JournalEntry::STATUS['GRN'],
                'reference_id'   => 'GRN - ' . $grnNumber,
                'debit'          => 0,
                'credit'         => $grandTotal,
            ],
        ];

        $this->storeJournalEntries($storeJournalEntryInteractor, $journalEntries);

        return response()->json($interactor->execute($id, $request));
    }

    public function grnItemsToStockTable(StoreStockSheetInteractor $storeStockSheetInteractor, array $entries): void
    {
        foreach ($entries as $entry) {
            $stockRequest = StockSheetRequest::validateAndCreate($entry);
            $storeStockSheetInteractor->execute($stockRequest);
        }

    }

    public function storeJournalEntries(StoreJournalEntryInteractor $storeJournalEntryInteractor, array $entries): void
    {
        foreach ($entries as $entry) {
            $journalRequest = JournalEntryRequest::validateAndCreate($entry);
            $storeJournalEntryInteractor->execute($journalRequest);
        }
    }




}

