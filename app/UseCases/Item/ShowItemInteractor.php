<?php

namespace App\UseCases\Item;

use App\Models\Item;

class ShowItemInteractor
{
    public function execute(Item $item): Item
    {
        return $item->load(['supplier', 'category', 'subCategory']);
    }
}
