<?php

namespace App\UseCases\Supplier;

use App\Models\Supplier;
use App\UseCases\Supplier\Requests\SupplierRequest;

class StoreSupplierInteractor {
    public function execute(SupplierRequest $request) {
        return Supplier::create($request->toArray());
    }
}
