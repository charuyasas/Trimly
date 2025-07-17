<?php

namespace App\UseCases\StockSheet;

use App\Models\StockSheet;
use App\UseCases\StockSheet\Requests\StockSheetEntryDataRequest;
use Illuminate\Support\Collection;


class StoreStockSheetInteractor
{
    public function execute(StockSheetEntryDataRequest $stockSheetEntryDataRequest)
    {
        $stockSheet = StockSheet::create($stockSheetEntryDataRequest->toArray());
        return $stockSheet->toArray();
    }

}
