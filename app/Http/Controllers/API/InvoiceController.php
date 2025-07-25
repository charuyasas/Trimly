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
        request()->merge(['id' => $id]);
        $result = $finishInvoiceInteractor->execute($id, InvoiceRequest::validateAndCreate(request()));

        return response()->json($result);
    }

    public function loadItemDropdown(LoadItemDropdownInteractor $loadItemDropdownInteractor)
    {
        return response()->json($loadItemDropdownInteractor->execute(request('search_key')));
    }

    public function loadInvoiceDropdown(LoadInvoiceDropdownInteractor $loadInvoiceDropdownInteractor)
    {
        return response()->json($loadInvoiceDropdownInteractor->execute());
    }

    public function getInvoiceItems($id, GetInvoiceItemsInteractor $getInvoiceItemsInteractor)
    {
        return response()->json($getInvoiceItemsInteractor->execute($id));


    }
}
