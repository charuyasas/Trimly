<?php

namespace App\UseCases\StockSheet;

use App\Models\StockSheet;

class GetAvailableStockInteractor
{
    public function execute(string $itemId, string $storeId): float
    {
        return StockSheet::where('item_code', $itemId)
            ->where('ledger_code', $storeId)
            ->selectRaw('SUM(debit) - SUM(credit) as available_stock')
            ->value('available_stock') ?? 0;
    }
}
