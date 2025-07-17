<?php

namespace App\UseCases\StockSheet\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class ItemRequest extends Data
{
    #[Rule(['required', 'uuid'])]
    public string $item_id;

    #[Rule(['required'])]
    public string $item_code;

    #[Rule(['required'])]
    public string $item_description;

    #[Rule(['required', 'numeric', 'not_in:0'])]
    public float|int $quantity;
}
