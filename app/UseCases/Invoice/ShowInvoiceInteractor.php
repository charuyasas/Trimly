<?php
namespace App\UseCases\Invoice;

use App\Models\Invoice;

 class ShowInvoiceInteractor {

    public function execute(Invoice $invoice)
{
    return $invoice->items()
        ->with('service:id,code')
        ->get()
        ->map(function ($item) {
            return [
                'item_id' => $item->item_id,
                'item_description' => $item->item_description,
                'quantity' => $item->quantity,
                'amount' => $item->amount,
                'discount_percentage' => $item->discount_percentage,
                'discount_amount' => $item->discount_amount,
                'sub_total' => $item->sub_total,
                'item_code' => $item->service->code
            ];
        });
}


 }
