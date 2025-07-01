<?php

namespace App\UseCases\Invoice;

use App\Models\Invoice;

class LoadInvoiceDropdownInteractor
{
    public function execute($search)
    {
        return Invoice::where('status', 0)
            ->where('id', 'like', "%$search%")
            ->limit(10)
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn($inv) => [
                'label' => $inv->invoice_no,
                'value' => $inv->id
            ]);
    }
}
