<?php

namespace App\UseCases\Invoice;

use App\Models\Service;

class LoadItemDropdownInteractor
{
    public function execute($search)
    {
        return Service::where('code', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->limit(10)
            ->orderBy('code', 'asc')
            ->get()
            ->map(fn($service) => [
                'label' => $service->code . ' - ' . $service->description,
                'value' => $service->id,
                'price' => $service->price
            ]);
    }
}
