<?php

namespace App\UseCases\StockSheet;

use App\Models\StockSheet;
use App\UseCases\StockSheet\Requests\StockSheetRequest;
use Illuminate\Support\Collection;


class StoreStockSheetInteractor extends Collection
{

    public function execute(StockSheetRequest $stockSheetRequest){
        $stockSheet = StockSheet::create($stockSheetRequest->toArray());
        return $stockSheet->toArray();
    }

}
