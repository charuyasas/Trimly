<?php

namespace App\UseCases\Grn\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use App\UseCases\Grn\Requests\GrnItemRequest;

class GrnRequest extends Data
{
    public string $grn_number;
    public string $grn_date;

    public string $supplier_id;
    public string $supplier_invoice_number;
    public string $grn_type;
    public ?string $store_location;

    public ?string $note;

    #[SpatieRule('sometimes','required')]
    public float $discount_amount;
    #[SpatieRule('sometimes','required')]
    public bool $is_percentage;

    #[SpatieRule('required', 'array')]
    #[DataCollectionOf(GrnItemRequest::class)]
    public array $items;
}
