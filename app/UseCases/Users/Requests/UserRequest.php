<?php

namespace App\UseCases\Users\Requests;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\{Email, Nullable, Required, Min, Sometimes};
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;

class UserRequest extends Data
{

    public ?string $id;

    #[nullable, Email]
    public ?string $email;

    #[SpatieRule('required')]
    #[Max(255)]
    public string $name;

    #[Sometimes, SpatieRule('string')]
    public ?string $username;

    #[SpatieRule('required', 'uuid')]
    public string $employee_id;

    #[SpatieRule('required')]
    #[Max(255)]
    public string $role;

    #[Sometimes, SpatieRule('string'), Min(6)]
    public ?string $password = null;

    public static function rules(): array
    {
        return [
            'username' => [
                'sometimes',
                'nullable',
                Rule::unique('users', 'username')->ignore(request()->input('id')),
            ],
        ];
    }
}


