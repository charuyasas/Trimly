<?php

namespace App\UseCases\Grn\Requests;

use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Data;

class GrnItemRequest extends Data
{
    #[SpatieRule('sometimes','required', 'string')]
    public string $item_id;

    #[SpatieRule('required', 'string')]
    public string $item_name;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $qty;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public float $foc;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $price;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public ?float $margin;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public ?float $discount;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $final_price;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $subtotal;
}
