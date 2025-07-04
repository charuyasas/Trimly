<?php

namespace App\UseCases\Supplier;

use App\Models\Supplier;

class DeleteSupplierInteractor {
    public function execute(Supplier $supplier): ?bool
    {
        return $supplier->delete();
    }
}
