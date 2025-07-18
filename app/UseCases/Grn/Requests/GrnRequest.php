<?php

namespace App\UseCases\Grn\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use App\UseCases\Grn\Requests\GrnItemRequest;
use Illuminate\Validation\Rule;
use App\Models\Grn;
use Illuminate\Http\Request;

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

    public static function rules(Request $request, array $params = []): array
    {
        $id = $params['id'] ?? $request->route('id');

        return [
            'grn_number' => [
                'required',
                'string',
                Rule::unique('grns', 'grn_number')
                    ->ignore($id)
                    ->where(function ($query) {
                        return $query->where('status', true); // Only validate against finalized GRNs
                    }),
            ],
        ];
    }
}
