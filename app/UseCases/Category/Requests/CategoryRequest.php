<?php

namespace App\UseCases\Category\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Illuminate\Validation\Rule;

class CategoryRequest extends Data
{
    public ?string $id;

    #[SpatieRule('required', 'max:255')]
    public string $name;

    public static function rules(): array
    {
        return [
            'name' => [
                Rule::unique('categories', 'name')->ignore(request()->input('id')),
            ],
        ];
    }
}
