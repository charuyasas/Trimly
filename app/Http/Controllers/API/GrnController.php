<?php

namespace App\Http\Controllers\API;

use App\Constance\AccountsLedgerCodes;
use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\StockSheet;
use App\UseCases\Grn\ListGrnInteractor;
use App\UseCases\Grn\Requests\GrnRequest;
use App\UseCases\Grn\Requests\GrnItemRequest;
use App\UseCases\Grn\StoreGrnInteractor;
use App\UseCases\Grn\LoadGrnDropdownInteractor;
use App\UseCases\Grn\GetGrnDetailsInteractor;
use App\UseCases\Grn\FinalizeGrnInteractor;
use App\UseCases\Grn\DeleteGrnItemInteractor;
use App\UseCases\Grn\UpdateGrnItemInteractor;
use App\UseCases\JournalEntry\Requests\JournalEntryRequest;
use App\UseCases\JournalEntry\StoreJournalEntryInteractor;
use App\UseCases\StockSheet\Requests\StockSheetEntryDataRequest;
use App\UseCases\StockSheet\StoreStockSheetInteractor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrnController extends Controller
{

    public function index(ListGrnInteractor $listGrnInteractor): Collection
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
        $grnNumber = $request->grn_number;
        $grandTotal = $request->grand_total;

        $stockDebitEntryData = collect($request->items)->map(function ($item) use ($grnNumber) {
            $totalQty = ($item->qty ?? 0) + ($item->foc ?? 0);
            return [
                'item_code'     => $item->item_id ?? '',
                'ledger_code'   => AccountsLedgerCodes::LEDGER_CODES['MainStore'],
                'description'   => 'GRN - ' . ($item->item_name ?? ''),
                'debit'         => $totalQty,
                'reference_type' => StockSheet::STATUS['GRN'],
                'reference_id'   => 'GRN - ' . $grnNumber,
            ];
        })->toArray();
        $this->grnItemsToStockTable($storeStockSheetInteractor, $stockDebitEntryData);

        $journalEntries = [
            [
                'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['MainStore'],
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
            $stockRequest = StockSheetEntryDataRequest::validateAndCreate($entry);
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

    public function updateItem(GrnItemRequest $request, string $id): JsonResponse
    {
        $interactor = new UpdateGrnItemInteractor();
        $result = $interactor->execute($id, $request->toArray());

        if ($result['status'] === 200) {
            return response()->json(['message' => 'Item updated', 'item' => $result['item']]);
        }

        return response()->json(['message' => 'Failed to update item', 'error' => $result['error']], 500);
    }

    public function getItemCostDetails(Request $request)
    {
        $itemId = $request->query('item_id');

        if (!$itemId) {
            return response()->json(['message' => 'Missing item_id'], 400);
        }

        // Get last cost (latest GRN entry)
        $lastCost = DB::table('grn_items')
            ->where('item_id', $itemId)
            ->orderByDesc('created_at')
            ->limit(1)
            ->value('price');

        // Get average cost (sum of all GRN prices / count)
        $averageCost = DB::table('grn_items')
            ->where('item_id', $itemId)
            ->avg('price'); // Laravel handles nulls

        return response()->json([
            'last_cost' => $lastCost ?? 0.00,
            'avg_cost' => $averageCost ?? 0.00,
        ]);
    }

}

