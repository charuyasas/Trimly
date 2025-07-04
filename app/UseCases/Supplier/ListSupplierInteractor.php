<?php

namespace App\UseCases\Supplier;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;

class ListSupplierInteractor {
    public function execute(): Collection
    {
        return Supplier::all();
    }
}
