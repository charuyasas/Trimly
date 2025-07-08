<?php

namespace App\UseCases\Item;

use App\Models\Item;
use App\UseCases\Item\Requests\ItemRequest;

class UpdateItemInteractor
{
    public function execute(Item $item, ItemRequest $request)
    {
        $item->update($request->toArray());
        return $item;
    }
}
