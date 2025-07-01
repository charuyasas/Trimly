<?php

namespace App\UseCases\Invoice\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;

class FinishInvoiceRequest extends Data
{
    public ?float $discount_percentage;
    public ?float $discount_amount;

    public static function rules(): array
    {
        return [
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
