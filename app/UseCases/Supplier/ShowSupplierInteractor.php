<?php

namespace App\UseCases\Supplier;

use App\Models\Supplier;

class ShowSupplierInteractor {
    public function execute(Supplier $supplier): Supplier
    {
        return $supplier;
    }
}

