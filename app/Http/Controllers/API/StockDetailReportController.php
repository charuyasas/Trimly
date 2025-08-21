<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\Reports\StockDetailReportInteractor;
use App\UseCases\Reports\Requests\StockDetailReportRequest;
use Illuminate\Http\Request;

class StockDetailReportController extends Controller
{
    public function execute(Request $request, StockDetailReportInteractor $interactor)
    {
        $dto = StockDetailReportRequest::from($request);

        $validated = $dto->toArray();

        return response()->json(
            $interactor->execute($validated)
        );
    }
}
