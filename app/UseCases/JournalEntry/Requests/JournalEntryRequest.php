<?php

namespace App\UseCases\JournalEntry\Requests;

use Livewire\Attributes\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Illuminate\Validation\Rules\Exists;

class JournalEntryRequest extends Data
{
    public ?string $id;

    #[Rule(['required', new Exists('posting_account', 'ledger_code')])]
    public string $ledger_code;

    #[SpatieRule('required')]
    public string $reference_type;

    #[SpatieRule('required')]
    public string $reference_id;

    #[SpatieRule('sometimes', 'required', 'numeric')]
    public float $credit;

    #[SpatieRule('sometimes', 'required', 'numeric')]
    public float $debit;
}
