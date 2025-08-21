<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\UseCases\JournalEntry\StoreJournalEntryInteractor;
use App\UseCases\Reports\GetSalesSummaryInteractor;
use App\UseCases\StockSheet\GetAvailableStockInteractor;
use App\UseCases\StockSheet\StoreStockSheetInteractor;
use Illuminate\Http\JsonResponse;
use App\UseCases\Invoice\{DeleteInvoiceItemInteractor,
    StoreInvoiceInteractor,
    FinishInvoiceInteractor,
    LoadItemDropdownInteractor,
    LoadInvoiceDropdownInteractor,
    GetInvoiceItemsInteractor,
    ListInvoiceInteractor,
    ShowInvoiceInteractor};
use App\UseCases\Invoice\Requests\InvoiceRequest;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(ListInvoiceInteractor $listInvoiceInteractor)
    {
        return $listInvoiceInteractor->execute();
    }

    public function store(StoreInvoiceInteractor $storeInvoiceInteractor): JsonResponse
    {
        $invoice = $storeInvoiceInteractor->execute(InvoiceRequest::validateAndCreate(request()));
        return response()->json($invoice['response'], $invoice['status']);
    }

    public function show(Invoice $invoice, ShowInvoiceInteractor $showInvoiceInteractor)

    {
        return $showInvoiceInteractor->execute($invoice);

    }

    public function finishInvoice($id, FinishInvoiceInteractor $finishInvoiceInteractor, StoreJournalEntryInteractor $storeJournalEntryInteractor, StoreStockSheetInteractor $storeStockSheetInteractor, GetAvailableStockInteractor $getAvailableStockInteractor): JsonResponse
    {
        request()->merge(['id' => $id]);
        $result = $finishInvoiceInteractor->execute($id, InvoiceRequest::validateAndCreate(request()), $storeJournalEntryInteractor, $storeStockSheetInteractor, $getAvailableStockInteractor);

        return response()->json($result);
    }

    public function loadItemDropdown(LoadItemDropdownInteractor $loadItemDropdownInteractor): JsonResponse
    {
        return response()->json($loadItemDropdownInteractor->execute(request('search_key')));
    }

    public function loadInvoiceDropdown(LoadInvoiceDropdownInteractor $loadInvoiceDropdownInteractor): JsonResponse
    {
        return response()->json($loadInvoiceDropdownInteractor->execute());
    }

    public function getInvoiceItems($id, GetInvoiceItemsInteractor $getInvoiceItemsInteractor): JsonResponse
    {
        return response()->json($getInvoiceItemsInteractor->execute($id));
    }

    public function deleteItem($tokenNo, $itemID, DeleteInvoiceItemInteractor $deleteInvoiceItemInteractor): JsonResponse
    {
        $result = $deleteInvoiceItemInteractor->execute($tokenNo, $itemID);

        return response()->json($result['response'], $result['status']);
    }

    public function salesmanSummary(Request $request, GetSalesSummaryInteractor $getSalesSummaryInteractor): JsonResponse
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $item_type = $request->query('item_type');
        $report_type = $request->query('report_type');

        $result = $getSalesSummaryInteractor->execute($startDate, $endDate, $item_type, $report_type);

        return response()->json($result, $result['status']);
    }
}
