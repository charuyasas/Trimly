<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use App\UseCases\Invoice\{
    StoreInvoiceInteractor,
    FinishInvoiceInteractor,
    LoadItemDropdownInteractor,
    LoadInvoiceDropdownInteractor,
    GetInvoiceItemsInteractor,
    ListInvoiceInteractor,
    ShowInvoiceInteractor
};
use App\UseCases\Invoice\Requests\InvoiceRequest;

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

    public function finishInvoice($id, FinishInvoiceInteractor $finishInvoiceInteractor): JsonResponse
    {
        request()->merge(['id' => $id]);
        $result = $finishInvoiceInteractor->execute($id, InvoiceRequest::validateAndCreate(request()));

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
}
