<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\JournalEntry\StoreJournalEntryInteractor;
use App\UseCases\SupplierPayment\GetSupplierPendingGrnsInteractor;
use App\UseCases\SupplierPayment\ListSupplierPaymentInteractor;
use App\UseCases\SupplierPayment\Requests\SupplierPaymentRequest;
use App\UseCases\SupplierPayment\StoreSupplierPaymentInteractor;
use Illuminate\Http\JsonResponse;

class SupplierPaymentController extends Controller
{
    public function index(ListSupplierPaymentInteractor $listSupplierPaymentInteractor)
    {
        return $listSupplierPaymentInteractor->execute();
    }

    public function getSupplierGrns(string $code, GetSupplierPendingGrnsInteractor $getSupplierPendingGrnsInteractor): JsonResponse
    {
        $result = $getSupplierPendingGrnsInteractor->execute($code);
        return response()->json($result);
    }

    public function store(StoreSupplierPaymentInteractor $storeSupplierPaymentInteractor, StoreJournalEntryInteractor $storeJournalEntryInteractor): JsonResponse
    {
        $data = SupplierPaymentRequest::validateAndCreate(request()->all());
        $payment = $storeSupplierPaymentInteractor->execute($data, $storeJournalEntryInteractor);

        return response()->json($payment, 201);
    }


}
