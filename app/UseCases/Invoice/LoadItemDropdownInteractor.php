<?php

namespace App\UseCases\Invoice;

use App\Models\Item;
use App\Models\Service;

class LoadItemDropdownInteractor
{
    public function execute($search)
    {
//        return Service::where('code', 'like', "%$search%")
//            ->orWhere('description', 'like', "%$search%")
//            ->limit(10)
//            ->orderBy('code', 'asc')
//            ->get()
//            ->map(fn($service) => [
//                'label' => $service->code . ' - ' . $service->description,
//                'value' => $service->id,
//                'price' => $service->price
//            ]);
        $services = collect(Service::where('code', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orderBy('code', 'asc')
            ->limit(10)
            ->get()
            ->map(fn($service) => [
                'label' => $service->code . ' - ' . $service->description,
                'value' => $service->id,
                'price' => $service->price,
                'item_type'  => 'service'
            ]));

        $items = collect(Item::where('code', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orderBy('code', 'asc')
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'label' => $item->code . ' - ' . $item->description,
                'value' => $item->id,
                'price' => $item->retail_price,
                'item_type'  => 'item'
            ]));

        return $services
            ->merge($items)
            ->sortBy('label')
            ->values()
            ->take(10);
    }
}
