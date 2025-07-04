<?php

namespace App\UseCases\Supplier;

use App\Models\Supplier;
use App\UseCases\Supplier\Requests\SupplierRequest;

class UpdateSupplierInteractor {
    public function execute(Supplier $supplier, SupplierRequest $request): Supplier
    {
        $supplier->update($request->except('id')->toArray());
        return $supplier;
    }
}

