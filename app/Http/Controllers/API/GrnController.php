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

        return response()->json($interactor->execute($id, $request, $storeJournalEntryInteractor, $storeStockSheetInteractor));
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
        // Get last cost for finalized GRNs only
        $lastCost = DB::table('grn_items')
            ->join('grns', 'grn_items.grn_id', '=', 'grns.id')
            ->where('grn_items.item_id', $itemId)
            ->where('grns.status', 1)
            ->orderByDesc('grn_items.created_at')
            ->limit(1)
            ->value('grn_items.price');

        // Weighted average cost = sum(qty * price) / sum(qty) for finalized GRNs only
        $weightedAverageCost = DB::table('grn_items')
            ->join('grns', 'grn_items.grn_id', '=', 'grns.id')
            ->where('grn_items.item_id', $itemId)
            ->where('grns.status', 1)
            ->selectRaw('SUM(grn_items.qty * grn_items.price) / NULLIF(SUM(grn_items.qty), 0) as avg_price')
            ->value('avg_price');

        return response()->json([
            'last_cost' => round($lastCost ?? 0, 2),
            'avg_cost' => round($weightedAverageCost ?? 0, 2),
        ]);
    }

}

