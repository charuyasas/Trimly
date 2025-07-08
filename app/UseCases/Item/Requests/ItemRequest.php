<?php

namespace App\UseCases\Item\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Illuminate\Validation\Rule;

class ItemRequest extends Data
{
    public ?string $id;

    #[SpatieRule('nullable', 'max:255')]
    public ?string $code;

    #[SpatieRule('required', 'max:255')]
    public string $description;

    #[SpatieRule('required', 'max:255')]
    public ?string $rack_location;

    #[SpatieRule('required', 'uuid', 'exists:suppliers,id')]
    public string $supplier_id;

    #[SpatieRule('required', 'uuid', 'exists:categories,id')]
    public string $category_id;

    #[SpatieRule('required', 'uuid', 'exists:sub_categories,id')]
    public ?string $sub_category_id;

    #[SpatieRule('required', 'in:kg,g,unit,l,ml')]
    public string $measure_unit;

    #[SpatieRule('required', 'boolean')]
    public bool $is_active;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $list_price;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $retail_price;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $wholesale_price;

    public static function rules(): array
    {
        return [
            'code' => [
                'nullable',
                Rule::unique('items', 'code')->ignore(request()->input('id')),
            ],
        ];
    }
}
