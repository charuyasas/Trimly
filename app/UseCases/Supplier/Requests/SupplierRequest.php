<?php

namespace App\UseCases\Supplier\Requests;

use Spatie\LaravelData\Data;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\Validation\Max;

class SupplierRequest extends Data
{
    public ?string $id;

    #[SpatieRule('required'), Max(255)]
    public string $supplier_code;

    #[SpatieRule('required'), Max(255)]
    public string $name;

    #[SpatieRule('nullable', 'digits:10')]
    public ?string $contact_no;

    #[SpatieRule('nullable', 'email'), Max(255)]
    public ?string $email;

    #[SpatieRule('nullable'), Max(255)]
    public ?string $address;

    #[SpatieRule('sometimes','required')]
    public string $ledger_code;

    public static function rules(): array
    {
        return [
            'supplier_code' => [
                'required',
                Rule::unique('suppliers', 'supplier_code')->ignore(request()->input('id')),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('suppliers', 'email')->ignore(request()->input('id')),
            ],
            'contact_no' => [
                'nullable',
                'digits:10',
                Rule::unique('suppliers', 'contact_no')->ignore(request()->input('id')),
            ],
        ];
    }
}
