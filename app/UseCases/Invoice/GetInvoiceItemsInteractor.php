<?php

namespace App\UseCases\Invoice;

use App\Models\Invoice;

class GetInvoiceItemsInteractor
{
    public function execute($id)
    {
        $invoice = Invoice::with(['items', 'employee', 'customer'])->findOrFail($id);

        return [
            'invoice_id' => $invoice->id,
            'invoice_no' => $invoice->invoice_no,
            'employee_no' => $invoice->employee_no,
            'employee_name' => $invoice->employee->name ?? '',
            'customer_no' => $invoice->customer_no,
            'customer_name' => $invoice->customer->name ?? '',
            'token_no' => $invoice->invoice_no,
            'discount_percentage' => $invoice->discount_percentage,
            'discount_amount' => $invoice->discount_amount,
            'items' => $invoice->items->makeHidden(['created_at', 'updated_at']),
        ];
    }
}
