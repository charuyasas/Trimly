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
use App\UseCases\Grn\DeleteGrnItemInteractor;
use App\UseCases\Grn\UpdateGrnItemInteractor;
use App\UseCases\JournalEntry\Requests\JournalEntryRequest;
use App\UseCases\JournalEntry\StoreJournalEntryInteractor;
use App\UseCases\StockSheet\Requests\StockSheetRequest;
use App\UseCases\StockSheet\StoreStockSheetInteractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
                'ledger_code'   => '1-2-6-1000',
                'description'   => 'GRN - ' . ($item->item_name ?? ''),
                'debit'         => $item->qty ?? 0,
            ];
        })->toArray();
        $this->grnItemsToStockTable($storeStockSheetInteractor, $stockdebitEntryData);

        $grnNumber = $request->grn_number;
        $grandTotal = $request->grand_total;

        $journalEntries = [
            [
                'ledger_code'    => '1-2-6-1000',
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

    public function deleteItem(string $id): JsonResponse
    {
        $interactor = new DeleteGrnItemInteractor();
        $result = $interactor->execute($id);

        if ($result['status'] === 200) {
            return response()->json(['message' => 'Item deleted'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete item', 'error' => $result['error']], 500);
        }
    }

    public function updateItem(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'qty' => 'required|numeric|min:0',
            'foc' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'margin' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'final_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $interactor = new UpdateGrnItemInteractor();
        $result = $interactor->execute($id, $validated);

        if ($result['status'] === 200) {
            return response()->json(['message' => 'Item updated', 'item' => $result['item']]);
        } else {
            return response()->json(['message' => 'Failed to update item', 'error' => $result['error']], 500);
        }
    }

}

