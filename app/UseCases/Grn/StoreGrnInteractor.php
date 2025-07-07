<?php

namespace App\UseCases\Grn;

use App\Models\Grn;
use App\UseCases\Grn\Requests\GrnRequest;

class StoreGrnInteractor {
    public function execute(GrnRequest $request) {
        return Grn::create($request->toArray());
    }
}
