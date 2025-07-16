<?php

namespace App\UseCases\Grn\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use App\UseCases\Grn\Requests\GrnItemRequest;
use Illuminate\Validation\Rule;
use App\Models\Grn;

class GrnRequest extends Data
{
    #[SpatieRule('required', 'string')]
    public string $grn_number;

    #[SpatieRule('required', 'date')]
    public string $grn_date;

    #[SpatieRule('required', 'string')]
    public string $supplier_id;

    #[SpatieRule('required', 'string')]
    public string $supplier_invoice_number;

    #[SpatieRule('required', 'in:Profit Margin,Discount Based')]
    public string $grn_type;

    #[SpatieRule('nullable', 'string')]
    public ?string $store_location;

    #[SpatieRule('nullable', 'string')]
    public ?string $note;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public ?float $grand_total = null;

    #[SpatieRule('nullable')]
    public string $supplier_ledger_code;

    #[SpatieRule('sometimes','required')]
    public float $discount_amount;

    #[SpatieRule('sometimes','required')]
    public bool $is_percentage;

    #[SpatieRule('required', 'array')]
    #[DataCollectionOf(GrnItemRequest::class)]
    public array $items;

    public static function rules(): array
    {
        return [
            'grn_number' => [
                Rule::unique(Grn::class, 'grn_number')->ignore(request('id'))
            ],
        ];
    }
}
