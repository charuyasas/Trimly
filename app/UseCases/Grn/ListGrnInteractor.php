<?php

namespace App\UseCases\Grn;

use App\Models\Grn;

class ListGrnInteractor {
    public function execute() {
        return Grn::with('supplier')->latest()->get();
    }
}
