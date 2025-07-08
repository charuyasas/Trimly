<?php

namespace App\UseCases\Item;

use App\Models\Item;

class ListItemInteractor
{
    public function execute()
    {
        return Item::with(['supplier', 'category', 'subCategory'])->latest()->get();
    }
}
