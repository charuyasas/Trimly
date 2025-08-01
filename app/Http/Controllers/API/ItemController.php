<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use App\UseCases\Item\Requests\ItemRequest;
use App\UseCases\Item\StoreItemInteractor;
use App\UseCases\Item\UpdateItemInteractor;
use App\UseCases\Item\DeleteItemInteractor;
use App\UseCases\Item\ShowItemInteractor;
use App\UseCases\Item\ListItemInteractor;
use App\UseCases\Reports\StockValueReportInteractor;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    public function index(ListItemInteractor $interactor)
    {
        return $interactor->execute();
    }

    public function store(StoreItemInteractor $interactor): JsonResponse
    {
        $item = $interactor->execute(ItemRequest::validateAndCreate(request()->all()));
        return response()->json($item, 201);
    }

    public function show(Item $item, ShowItemInteractor $interactor)
    {
        return $interactor->execute($item);
    }

    public function update(Item $item, UpdateItemInteractor $interactor): JsonResponse
    {
        $item = $interactor->execute($item, ItemRequest::validateAndCreate(request()));
        return response()->json($item);
    }

    public function destroy(Item $item, DeleteItemInteractor $interactor): JsonResponse
    {
        $interactor->execute($item);
        return response()->json(null, 204);
    }

    public function loadItemDropdown(Request $request)
    {
        $searchKey = $request->input('search_key');

        $items = Item::where('description', 'like', "%{$searchKey}%")
            ->orWhere('code', 'like', "%{$searchKey}%")
            ->limit(10)
            ->get();

        $results = $items->map(function ($item) {
            return [
                'label' => "{$item->code} - {$item->description}",
                'value' => $item->id,
                'retail_price' => $item->retail_price
            ];
        });

        return response()->json($results);
    }
    public function stockValueReport(StockValueReportInteractor $interactor)
    {
        return response()->json($interactor->execute());
    }

}
