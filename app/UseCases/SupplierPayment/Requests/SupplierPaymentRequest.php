<?php

namespace App\UseCases\SupplierPayment\Requests;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class SupplierPaymentRequest extends Data
{
    public ?string $id;

    #[Rule(['required', 'uuid', 'exists:suppliers,id'])]
    public string $supplier_id;

    #[Rule(['required', 'in:cash,bank'])]
    public string $payment_type;

    #[Rule(['required', 'numeric', 'min:0.01'])]
    public float $amount;

    #[Rule(['required', 'array'])]
    /** @var DataCollection<GRNPaymentData> */
    public DataCollection $payments;

    #[Rule(['required_if:payment_type,bank', 'string'])]
    public ?string $bank_id;

    // Optional for BANK payments
    #[Rule(['required_if:payment_type,bank', 'string'])]
    public ?string $bank_slip_no;

    #[Rule(['required_if:payment_type,bank', 'date'])]
    public ?string $date;
}
