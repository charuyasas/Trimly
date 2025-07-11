<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\Grn\Requests\GrnRequest;
use App\UseCases\Grn\StoreGrnInteractor;

class GrnController extends Controller
{
    public function store(StoreGrnInteractor $storeGrnInteractor)
    {
        $validated = GrnRequest::validateAndCreate(request());
        $response = $storeGrnInteractor->execute($validated);

        return response()->json($response['response'], $response['status']);
    }
}

