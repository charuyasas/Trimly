<?php

namespace App\UseCases\Expenses\Requests;

use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Data;

class ExpensesRequest extends Data
{
    public ?int $id;

    #[SpatieRule('sometimes','required')]
    public string $debit_account;

    #[SpatieRule('required', 'max:255')]
    public string $description;

    #[SpatieRule('required', 'numeric', 'min:0')]
    public float $amount;

    #[SpatieRule('sometimes','required', 'exists:users,id')]
    public int $user_id;
}
