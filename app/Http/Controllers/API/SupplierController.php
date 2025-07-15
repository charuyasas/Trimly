<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\UseCases\Supplier\Requests\SupplierRequest;
use App\UseCases\Supplier\ListSupplierInteractor;
use App\UseCases\Supplier\StoreSupplierInteractor;
use App\UseCases\Supplier\ShowSupplierInteractor;
use App\UseCases\Supplier\UpdateSupplierInteractor;
use App\UseCases\Supplier\DeleteSupplierInteractor;
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;
use App\UseCases\PostingAccount\StorePostingAccountInteractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;

class SupplierController extends Controller
{
    public function index(ListSupplierInteractor $interactor): Collection
    {
        return $interactor->execute();
    }

    public function store(
        StoreSupplierInteractor $interactor,
        StorePostingAccountInteractor $postingAccountInteractor
    ): JsonResponse {
        $ledgerCode = $this->createLedgerCode($postingAccountInteractor);

        $supplierData = array_merge(
            request()->all(),
            ['ledger_code' => $ledgerCode]
        );

        $supplier = $interactor->execute(SupplierRequest::validateAndCreate($supplierData));
        return response()->json($supplier, 201);
    }

    public function show(Supplier $supplier, ShowSupplierInteractor $interactor): Supplier
    {
        return $interactor->execute($supplier);
    }

    public function update(Supplier $supplier, UpdateSupplierInteractor $interactor): JsonResponse
    {
        return response()->json($interactor->execute($supplier, SupplierRequest::validateAndCreate(request())));
    }

    public function destroy(Supplier $supplier, DeleteSupplierInteractor $interactor): JsonResponse
    {
        $interactor->execute($supplier);
        return response()->json(null, 204);
    }

    public function loadSupplierDropdown()
    {
        return response()->json(
            Supplier::where('name', 'like', '%' . request('search_key') . '%')
                ->orderBy('name')
                ->limit(10)
                ->get()
                ->map(fn($supplier) => [
                    'label' => $supplier->name,
                    'value' => $supplier->id,
                    'ledger_code' => $supplier->ledger_code
                ])
        );
    }

    private function createLedgerCode(StorePostingAccountInteractor $postingAccountInteractor): string
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
