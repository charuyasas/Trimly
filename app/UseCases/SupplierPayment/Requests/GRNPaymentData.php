<?php

namespace App\UseCases\SupplierPayment\Requests;

use Spatie\LaravelData\Data;

class GRNPaymentData extends Data
{
    #[Rule(['required', 'string', 'exists:grns,id'])]
    public string $grn_no;

    #[Rule(['required', 'numeric', 'min:0.01'])]
    public float $amount;
}
