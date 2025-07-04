<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\UseCases\Supplier\Requests\SupplierRequest;
use App\UseCases\Supplier\ListSupplierInteractor;
use App\UseCases\Supplier\StoreSupplierInteractor;
use App\UseCases\Supplier\ShowSupplierInteractor;
use App\UseCases\Supplier\UpdateSupplierInteractor;
use App\UseCases\Supplier\DeleteSupplierInteractor;
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;
use App\UseCases\PostingAccount\StorePostingAccountInteractor;

class SupplierController extends Controller
{
    public function index(ListSupplierInteractor $interactor)
    {
        return $interactor->execute();
    }

    public function store(StoreSupplierInteractor $interactor, StorePostingAccountInteractor $postingAccountInteractor)
    {
        $ledgerCode = $this->createLedgerCode($postingAccountInteractor);

        $supplierData = array_merge(
            request()->all(),
            ['ledger_code' => $ledgerCode]
        );

        $supplier = $interactor->execute(SupplierRequest::validateAndCreate($supplierData));
        return response()->json($supplier, 201);
    }

    public function show(Supplier $supplier, ShowSupplierInteractor $interactor)
    {
        return $interactor->execute($supplier);
    }

    public function update(Supplier $supplier, UpdateSupplierInteractor $interactor)
    {
        return response()->json($interactor->execute($supplier, SupplierRequest::validateAndCreate(request())));
    }

    public function destroy(Supplier $supplier, DeleteSupplierInteractor $interactor)
    {
        $interactor->execute($supplier);
        return response()->json(null, 204);
    }

    public function createLedgerCode(StorePostingAccountInteractor $postingAccountInteractor)
    {
        $data = [
            'posting_code'     => null,
            'posting_account'  => 'Supplier Account',
            'main_code'        => 4,
            'heading_code'     => 9,
            'title_code'       => 10,
        ];

        $newPostingAccount = $postingAccountInteractor->execute(PostingAccountRequest::validateAndCreate($data));
        return $newPostingAccount['ledger_code'];
    }
}

