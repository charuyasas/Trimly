<?php

namespace App\UseCases\Item;

use App\Models\Item;
use App\UseCases\Item\Requests\ItemRequest;
use Illuminate\Support\Str;

class StoreItemInteractor
{
    public function execute(ItemRequest $request): Item
    {
        $data = $request->toArray();

        // Auto-generate item code if not provided
        if (empty($data['code'])) {
            $data['code'] = $this->generateUniqueCode();
        }

        return Item::create($data);
    }

    private function generateUniqueCode(): string
    {
        $lastCode = Item::where('code', 'REGEXP', '^[0-9]{4}$')
            ->orderByDesc('code')
            ->value('code');

        $nextNumber = $lastCode ? intval($lastCode) + 1 : 1;

        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

}
