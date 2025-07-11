<?php

namespace App\UseCases\Customer\Requests;

use Spatie\LaravelData\Data;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\Validation\Max;

class CustomerRequest extends Data
{
    public ?string $id;

    #[SpatieRule('required'), Max(255)]
    public string $name;

    #[SpatieRule('required'), Max(255)]
    public string $email;

    #[SpatieRule('nullable', 'string', 'max:10')]
    public ?string $phone;

    #[SpatieRule('nullable'), Max(255)]
    public ?string $address;

    #[SpatieRule('sometimes', 'required')]
    public string $ledger_code;

    public static function rules(): array
   {
    return [
        'email' => [
            'required',
            'email',
            Rule::unique('customers', 'email')->ignore(request()->input('id')), // <- this needs 'id'
        ],
        'phone' => [
            'nullable',
            Rule::unique('customers', 'phone')->ignore(request()->input('id')),
        ],
    ];
   }

}
