<?php

namespace App\UseCases\StockSheet\Requests;

use Livewire\Attributes\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Illuminate\Validation\Rules\Exists;

class StockSheetRequest extends Data
{
    public ?string $id;

    #[Rule(['required', new Exists('items', 'code')])]
    public string $item_code;

    #[Rule(['required', new Exists('posting_account', 'ledger_code')])]
    public string $ledger_code;

    #[SpatieRule('required')]
    public string $description;

    #[SpatieRule('sometimes', 'required', 'numeric')]
    public float $credit;

    #[SpatieRule('sometimes', 'required', 'numeric')]
    public float $debit;
}
