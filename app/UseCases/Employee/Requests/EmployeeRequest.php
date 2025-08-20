<?php

namespace App\UseCases\Employee\Requests;

use Spatie\LaravelData\Data;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\Validation\Max;

class EmployeeRequest extends Data
{
    public ?string $id;

    public string $employee_id;

    #[SpatieRule('required')]
    #[Max(255)]
    public string $name;

    #[SpatieRule('required')]
    #[Max(255)]
    public string $address;

    #[SpatieRule('required', 'digits:10')]
    public string $contact_no;

    #[SpatieRule('sometimes','required')]
    public string $ledger_code;

    public ?string $commission;

    public static function rules(): array
    {
        return [
            'employee_id' => [
                'required',
                Rule::unique('employees', 'employee_id')->ignore(request()->input('id')),
            ],
        ];
    }
}
