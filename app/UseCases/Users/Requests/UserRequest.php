<?php

namespace App\UseCases\Users\Requests;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\{Email, Required, Min, Sometimes};
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;

class UserRequest extends Data
{

    public ?string $id;

    #[Required, Email]
    public string $email;

    #[SpatieRule('required')]
    #[Max(255)]
    public string $name;

    #[SpatieRule('required')]
    #[Max(255)]
    public string $role;

    #[Sometimes, SpatieRule('string'), Min(6)]
    public ?string $password = null;

    public static function rules(): array
    {
        return [
            'email' => [
                'required',
                Rule::unique('users', 'email')->ignore(request()->input('id')),
            ],
        ];
    }
}


