<?php

namespace App\UseCases\Invoice;

use App\Models\Item;
use App\Models\Service;

class LoadItemDropdownInteractor
{
    public function execute($search)
    {
        $services = collect(Service::where('code', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orderBy('code', 'asc')
            ->limit(10)
            ->get()
            ->map(fn($service) => [
                'label' => $service->code . ' - ' . $service->description,
                'value' => $service->id,
                'price' => $service->price,
                'item_type'  => 'service',
                'fixed_price'  => $service->is_fixed_price
            ]));

        $items = collect(Item::where('code', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orderBy('code', 'asc')
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'label' => $item->code . ' - ' . $item->description,
                'value' => $item->id,
                'price' => $item->last_grn_price,
                'item_type'  => 'item',
                'fixed_price'  => 0
            ]));

        return $services
            ->merge($items)
            ->sortBy('label')
            ->values()
            ->take(10);
    }
}
