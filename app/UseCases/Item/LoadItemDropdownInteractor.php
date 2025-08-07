<?php

namespace App\UseCases\Item;

use App\Models\Item;

class LoadItemDropdownInteractor
{
    public function execute(string $searchKey)
    {
        return Item::where('description', 'like', "%{$searchKey}%")
            ->orWhere('code', 'like', "%{$searchKey}%")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'label' => "{$item->code} - {$item->description}",
                    'value' => $item->id,
                    'list_price'  => $item->list_price,
                ];
            });
    }
}
