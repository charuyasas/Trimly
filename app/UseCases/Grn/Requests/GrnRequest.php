<?php

namespace App\UseCases\Grn\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\Validation\Max;
use Illuminate\Validation\Rule;

class GrnRequest extends Data
{
    public ?string $id;

    #[SpatieRule('required'), Max(255)]
    public string $grn_number;

    #[SpatieRule('required', 'date')]
    public string $grn_date;

    #[SpatieRule('required', 'uuid')]
    public string $supplier_id;

    #[SpatieRule('required')]
    public string $supplier_invoice_number;

    #[SpatieRule('required')]
    public string $grn_type;

    #[SpatieRule('nullable'), Max(255)]
    public ?string $store_location;

    #[SpatieRule('nullable')]
    public ?string $note;

    public static function rules(): array {
        return [
            'grn_number' => [
                'required',
                Rule::unique('grns', 'grn_number')->ignore(request()->input('id')),
            ]
        ];
    }
}
