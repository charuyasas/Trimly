<?php

namespace App\UseCases\Invoice\Requests;

use Spatie\LaravelData\Data;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use App\UseCases\Invoice\Requests\InvoiceItemRequest;

class InvoiceRequest extends Data
{
    public ?string $id;

    #[SpatieRule('nullable')]
    public ?string $token_no;

    #[SpatieRule('nullable')]
    public ?string $invoice_no;

    #[SpatieRule('required', 'uuid')]
    public string $employee_no;

    #[SpatieRule('required', 'uuid')]
    public string $customer_no;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public ?float $grand_total = null;

    #[SpatieRule('nullable', 'integer', 'min:0', 'max:100')]
    public int $discount_percentage = 0;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public float $discount_amount = 0.00;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public ?float $received_cash;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public ?float $balance;

    #[SpatieRule('boolean')]
    public bool $status = true;

    #[SpatieRule('required', 'array')]
    #[DataCollectionOf(InvoiceItemRequest::class)]
    public array $items;

    public static function rules(): array
    {
        return [
            'invoice_no' => [
                'sometimes',
                'nullable',
                Rule::unique('invoices', 'invoice_no')->ignore(request()->input('id')),
            ],
        ];
    }
}
