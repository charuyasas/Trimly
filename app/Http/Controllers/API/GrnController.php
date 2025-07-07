<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use App\UseCases\Grn\Requests\GrnRequest;
use App\UseCases\Grn\StoreGrnInteractor;
use App\UseCases\Grn\ListGrnInteractor;
use Illuminate\Http\JsonResponse;

class GrnController extends Controller
{
    public function index(ListGrnInteractor $interactor): JsonResponse {
        return response()->json($interactor->execute());
    }

    public function store(StoreGrnInteractor $interactor): JsonResponse {
        $grn = $interactor->execute(GrnRequest::validateAndCreate(request()->all()));
        return response()->json($grn, 201);
    }
}
