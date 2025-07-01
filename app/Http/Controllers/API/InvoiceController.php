<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
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
use App\UseCases\Invoice\Requests\FinishInvoiceRequest;

class InvoiceController extends Controller
{
    public function index(ListInvoiceInteractor $listInvoiceInteractor)
    {
        return $listInvoiceInteractor->execute();
    }

    public function store(StoreInvoiceInteractor $storeInvoiceInteractor)
    {
        $invoice = $storeInvoiceInteractor->execute(InvoiceRequest::validateAndCreate(request()));
        return response()->json($invoice['response'], $invoice['status']);
    }

    public function show(Invoice $invoice, ShowInvoiceInteractor $showInvoiceInteractor)
    {
        return $showInvoiceInteractor->execute($invoice);
    }

    public function finishInvoice($id, FinishInvoiceInteractor $finishInvoiceInteractor)
    {
        $result = $finishInvoiceInteractor->execute($id, FinishInvoiceRequest::validateAndCreate(request()));
        return response()->json($result);
    }

    public function loadItemDropdown(LoadItemDropdownInteractor $loadItemDropdownInteractor)
    {
        return response()->json($loadItemDropdownInteractor->execute(request('q')));
    }

    public function loadInvoiceDropdown(LoadInvoiceDropdownInteractor $loadInvoiceDropdownInteractor)
    {
        return response()->json($loadInvoiceDropdownInteractor->execute(request('q')));
    }

    public function getInvoiceItems($id, GetInvoiceItemsInteractor $getInvoiceItemsInteractor)
    {
        return response()->json($getInvoiceItemsInteractor->execute($id));
    }
}
