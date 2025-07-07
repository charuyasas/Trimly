<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\Grn\Requests\GrnRequest;
use App\UseCases\Grn\StoreGrnInteractor;
use App\UseCases\Grn\LoadGrnDropdownInteractor;
use App\UseCases\Grn\GetGrnDetailsInteractor;
use App\UseCases\Grn\FinishGrnInteractor;

class GrnController extends Controller
{
    public function store(StoreGrnInteractor $storeGrnInteractor)
    {
        $validated = GrnRequest::validateAndCreate(request());
        $response = $storeGrnInteractor->execute($validated);

        return response()->json($response['response'], $response['status']);
    }

    public function loadGrnDropdown(LoadGrnDropdownInteractor $interactor)
    {
        return response()->json($interactor->execute(request('search_key')));
    }

    public function getGrnDetails($id, GetGrnDetailsInteractor $interactor)
    {
        return response()->json(['grn' => $interactor->execute($id)]);
    }

    public function finalize($id, GrnRequest $request, FinishGrnInteractor $interactor)
    {
        return response()->json($interactor->execute($id, $request));
    }

}

