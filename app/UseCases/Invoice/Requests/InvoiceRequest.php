<?php

namespace App\UseCases\Invoice\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;

class InvoiceRequest extends Data
{
    public ?string $invoice_no;
    public string $employee_no;
    public string $customer_no;
    public ?int $discount_percentage;
    public ?int $discount_amount;
    public array $items;

    public static function rules(): array
    {
        return [
            'employee_no' => ['required'],
            'customer_no' => ['required'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required'],
            'items.*.item_description' => ['required'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percentage' => ['nullable', 'integer', 'min:0'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.sub_total' => ['required', 'numeric', 'min:0'],
        ];
    }
}
