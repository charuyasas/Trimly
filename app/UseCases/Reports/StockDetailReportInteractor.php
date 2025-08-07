<?php

namespace App\UseCases\Reports;

use App\Models\Item;
use App\Models\StockSheet;
use Illuminate\Support\Collection;

class StockDetailReportInteractor
{
    public function execute(array $data): array
    {
        $itemIds = $data['item_ids'] ?? [];
        $startDate = $data['start_date'];
        $endDate = $data['end_date'];
        $store = $data['store'];

        if (count($itemIds) > 0) {
            // If specific items are selected
            $items = Item::whereIn('id', $itemIds)->get(['id', 'description']);
        } else {
            // Find items that have stock records in given store and date
            $matchedItemIds = StockSheet::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('ledger_code', $store)
                ->pluck('item_code')
                ->unique()
                ->toArray();

            $items = Item::whereIn('id', $matchedItemIds)->get(['id', 'description']);
        }

        $results = [];

        foreach ($items as $item) {
            $stockSheets = StockSheet::where('item_code', $item->id)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('ledger_code', $store)
                ->get();

            $transactions = $stockSheets->groupBy('reference_id')->map(function ($group) {
                return [
                    'reference_type' => $group->first()->reference_id,
                    'debit' => $group->sum('debit'),
                    'credit' => $group->sum('credit'),
                ];
            })->values()->toArray();

            $results[] = [
                'item_id' => $item->id,
                'description' => $item->description,
                'transactions' => $transactions,
            ];
        }

        return $results;
    }

}
