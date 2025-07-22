<?php

namespace App\UseCases\Invoice;

use App\Models\Invoice;

class LoadInvoiceDropdownInteractor
{
    public function execute()
    {
        return Invoice::where('status', 0)
            ->limit(10)
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn($inv) => [
                'label' => $inv->token_no,
                'value' => $inv->id
            ]);
    }
}
