<?php

namespace App\UseCases\Role\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Illuminate\Validation\Rule;

class RoleRequest extends Data
{
    public ?string $id;

    #[SpatieRule('required', 'string', 'max:255')]
    public string $name;

    #[SpatieRule('sometimes', 'string', 'max:255')]
    public ?string $guard_name;

    public static function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore(request()->input('id')),
            ],
        ];
    }
} 