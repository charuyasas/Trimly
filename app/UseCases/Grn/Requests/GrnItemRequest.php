<?php

namespace App\UseCases\Grn\Requests;

use Spatie\LaravelData\Data;

class GrnItemRequest extends Data
{
    public string $item_id;
    public string $item_name;
    public float $qty;
    public float $foc;

    public float $price;

    public ?float $margin;

    public ?float $discount;
    public float $final_price;

    public float $subtotal;
}
