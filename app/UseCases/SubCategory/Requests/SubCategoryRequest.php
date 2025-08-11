<?php

namespace App\UseCases\SubCategory\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Illuminate\Validation\Rule;

class SubCategoryRequest extends Data
{
    public ?string $id;

    #[SpatieRule('required', 'max:255')]
    public string $name;

    public static function rules(): array
    {
        return [
            'name' => [
                Rule::unique('sub_categories', 'name')->ignore(request()->input('id')),
            ],
        ];
    }
}
