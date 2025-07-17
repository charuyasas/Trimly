<?php

namespace App\UseCases\StockSheet\Requests;

use Livewire\Attributes\Rule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Illuminate\Validation\Rules\Exists;

class StockSheetRequest extends Data
{
    public ?string $id;

    #[Rule(['required', new Exists('posting_account', 'ledger_code')])]
    public string $employee_ledger_code;

    #[Rule(['nullable', new Exists('posting_account', 'ledger_code')])]
    public ?string $store_ledger_code;

    #[SpatieRule('required', 'array')]
    #[DataCollectionOf(ItemRequest::class)]
    public array $items;
}
