<?php

namespace App\UseCases\StockSheet\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class StockSheetEntryDataRequest extends Data
{
    #[Rule(['required', 'uuid'])]
    public string $item_code;

    #[Rule(['required'])]
    public string $ledger_code;

    #[Rule(['required'])]
    public string $description;

    #[Rule(['required', 'numeric'])]
    public float|int $debit = 0;

    #[Rule(['required', 'numeric'])]
    public float|int $credit = 0;

    #[Rule(['required'])]
    public string $reference_type;

    #[Rule(['required'])]
    public string $reference_id;
}

