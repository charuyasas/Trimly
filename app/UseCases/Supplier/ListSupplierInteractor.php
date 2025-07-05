<?php

namespace App\UseCases\Supplier;

use App\Models\Supplier;

class ListSupplierInteractor {
    public function execute() {
        return Supplier::all();
    }
}
