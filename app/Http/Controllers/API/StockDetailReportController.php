<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\Reports\StockDetailReportInteractor;
use Illuminate\Http\Request;

class StockDetailReportController extends Controller
{
    public function execute(Request $request, StockDetailReportInteractor $interactor)
    {
        $validated = $request->validate([
            'item_ids' => 'sometimes|array',
            'item_ids.*' => 'uuid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'store' => 'required|string',
        ]);

        $validated['item_ids'] = $validated['item_ids'] ?? [];

        return response()->json(
            $interactor->execute($validated)
        );
    }
}
