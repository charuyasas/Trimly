<?php

namespace App\UseCases\Invoice\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\Validation\Max;

class InvoiceItemRequest extends Data
{
    public ?string $id;

    #[SpatieRule('nullable', 'uuid')]
    public ?string $invoice_id = null;

    #[SpatieRule('required')]
    #[Max(255)]
    public string $item_id;

    #[SpatieRule('required')]
    #[Max(1000)]
    public string $item_description;

    #[SpatieRule('required')]
    #[Max(1000)]
    public string $item_type;

    #[SpatieRule('required', 'integer', 'min:1')]
    public int $quantity;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $amount;

    #[SpatieRule('sometimes', 'nullable', 'integer', 'min:0', 'max:100')]
    public ?int $discount_percentage = 0;

    #[SpatieRule('sometimes', 'nullable', 'numeric', 'min:0')]
    public ?float $discount_amount = 0.00;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $sub_total;
}
