<?php

namespace App\UseCases\Reports\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;

class StockDetailReportRequest extends Data
{
    #[SpatieRule('sometimes', 'array')]
    public array $item_ids = [];

    #[SpatieRule('required', 'date')]
    public string $start_date;

    #[SpatieRule('required', 'date')]
    public string $end_date;

    #[SpatieRule('required', 'string')]
    public string $store;

    public static function rules(): array
    {
        return [
            'item_ids.*' => ['uuid'],
            'end_date'   => ['after_or_equal:start_date'],
        ];
    }

    public static function messages(): array
    {
        return [
            'item_ids.*.uuid'         => 'Each item ID must be a valid UUID.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
        ];
    }
}
