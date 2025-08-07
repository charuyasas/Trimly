<?php

namespace App\UseCases\Invoice;

use App\Models\Invoice;
use App\Models\SystemConfiguration;

class LoadInvoiceDropdownInteractor
{
    public function execute(): array
    {
        // Fetch invoices
        $invoices = Invoice::where('status', 0)
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn($inv) => [
                'label' => $inv->token_no,
                'value' => $inv->id
            ]);

        // Fetch max discount
        $config = SystemConfiguration::where('configuration_name', 'Discount Percentages')->first();
        $maxDiscount = $config?->configuration_data['Maximum Discount Percentage'] ?? null;

        return [
            'invoices' => $invoices,
            'max_discount_percentage' => $maxDiscount,
        ];
    }
}
