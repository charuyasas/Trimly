<?php

namespace App\Http\Controllers\API;

use App\Constance\AccountsLedgerCodes;
use App\Http\Controllers\Controller;
use App\Models\StockSheet;
use App\UseCases\StockSheet\GetAvailableStockInteractor;
use App\UseCases\StockSheet\ListItemIssueInteractor;
use App\UseCases\StockSheet\LoadStoreDropdownInteractor;
use App\UseCases\StockSheet\Requests\StockSheetEntryDataRequest;
use App\UseCases\StockSheet\Requests\StockSheetRequest;
use App\UseCases\StockSheet\ShowStockIssueInteractor;
use App\UseCases\StockSheet\StoreStockSheetInteractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(ListItemIssueInteractor $listItemIssueInteractor)
    {
        return $listItemIssueInteractor->execute();
    }

    public function show(string $referenceId, ShowStockIssueInteractor $showInvoiceInteractor): JsonResponse
    {
        $data = $showInvoiceInteractor->execute($referenceId);
        return response()->json($data);
    }

    public function getAvailableStock(Request $request, GetAvailableStockInteractor $getAvailableStockInteractor): JsonResponse
    {
        $itemId = $request->query('item_id');
        $storeLedger = $request->query('store');
        $availableStock = $getAvailableStockInteractor->execute($itemId,$storeLedger);
        return response()->json(['available_stock' => $availableStock]);
    }

    public function store(StockSheetRequest $stockSheetRequest, StoreStockSheetInteractor $storeStockSheetInteractor): JsonResponse
    {
        $employee_ledger_code = $stockSheetRequest->employee_ledger_code;
        $store_ledger_code = $stockSheetRequest->store_ledger_code;

        if($store_ledger_code != '') {
            $nextReferenceId = $this->createReferenceID(StockSheet::STATUS['Employee Issue']);
            $debitEntries = $this->createDebitEntryCollection($stockSheetRequest->items, $store_ledger_code, $nextReferenceId, StockSheet::STATUS['Employee Issue']);
            $creditEntries = $this->createCreditEntryCollection($stockSheetRequest->items, $employee_ledger_code, $nextReferenceId, StockSheet::STATUS['Employee Issue']);
            $this->employeeIssueItemsToStockTable($storeStockSheetInteractor, $debitEntries->merge($creditEntries));
        }else{
            $nextReferenceId = $this->createReferenceID(StockSheet::STATUS['Employee Consumption']);
            $debitEntries = $this->createDebitEntryCollection($stockSheetRequest->items, $employee_ledger_code, $nextReferenceId, StockSheet::STATUS['Employee Consumption'], StockSheet::STATUS['Employee Consumption']);
            $this->employeeIssueItemsToStockTable($storeStockSheetInteractor, $debitEntries);
        }


        return response()->json([
            'message'       => 'Stock entries finalized successfully.',
            'reference_id'  => $nextReferenceId,
        ]);
    }

    private function createCreditEntryCollection(array $items, string $ledgerCode, string $referenceId, string $referenceType): Collection
    {
        return collect($items)->map(function ($item) use ($referenceType, $ledgerCode, $referenceId) {
            return [
                'item_code'      => $item->item_id ?? '',
                'ledger_code'    => $ledgerCode,
                'description'    => $referenceType . ' - ' . ($item->item_description ?? ''),
                'debit'          => 0,
                'credit'         => $item->quantity,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
            ];
        });
    }

    private function createDebitEntryCollection(array $items, string $ledgerCode, string $referenceId, string $referenceType): Collection
    {
        return collect($items)->map(function ($item) use ($referenceType, $ledgerCode, $referenceId) {
            return [
                'item_code'      => $item->item_id ?? '',
                'ledger_code'    => $ledgerCode,
                'description'    => $referenceType . ' - ' . ($item->item_description ?? ''),
                'debit'          => $item->quantity,
                'credit'         => 0,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
            ];
        });
    }

    public function employeeIssueItemsToStockTable(StoreStockSheetInteractor $storeStockSheetInteractor, Collection $entries): void
    {
        $entries->each(function ($entry) use ($storeStockSheetInteractor) {
            $stockRequest = StockSheetEntryDataRequest::validateAndCreate($entry);
            $storeStockSheetInteractor->execute($stockRequest);
        });
    }

    public function createReferenceID(string $referenceType): string
    {
        $latestRefId = DB::table('stock_sheets')
            ->where('reference_type', $referenceType)
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(reference_id, " - ", -1) AS UNSIGNED)) as max_ref')
            ->value('max_ref');

        $nextRefNumber = ((int) $latestRefId) + 1;
        return $referenceType . ' - ' . str_pad((string) $nextRefNumber, 3, '0', STR_PAD_LEFT);
    }
    public function loadStoreDropdown(LoadStoreDropdownInteractor $loadStoreDropdownInteractor): JsonResponse
    {
        return response()->json($loadStoreDropdownInteractor->execute(request('search_key')));
    }
}
