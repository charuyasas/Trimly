<?php

namespace App\UseCases\CashTransfer\Requests;

use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Data;

class CashTransferRequest extends Data
{
    public ?int $id;

    #[SpatieRule('required')]
    public string $credit_account;

    #[SpatieRule('required')]
    public string $debit_account;

    #[SpatieRule('required', 'max:255')]
    public string $description;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $amount;

    #[SpatieRule('sometimes','required', 'exists:users,id')]
    public int $user_id;
}
