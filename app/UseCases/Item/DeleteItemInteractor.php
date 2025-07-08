<?php

namespace App\UseCases\Item;

use App\Models\Item;

class DeleteItemInteractor
{
    public function execute(Item $item): ?bool
    {
        return $item->delete();
    }
}
