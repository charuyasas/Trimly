<?php

namespace App\UseCases\Grn;

use App\Models\Grn;

class GetGrnDetailsInteractor
{
    public function execute($id)
    {
        $grn = Grn::with(['items', 'supplier'])->findOrFail($id);

        return [
            'id' => $grn->id,
            'grn_number' => $grn->grn_number,
            'grn_date' => $grn->grn_date,
            'supplier_id' => $grn->supplier_id,
            'supplier_name' => $grn->supplier->name ?? '',
            'supplier_invoice_number' => $grn->supplier_invoice_number,
            'supplier_ledger_code' => $grn->supplier->ledger_code ?? '',
            'grn_type' => $grn->grn_type,
            'note' => $grn->note,
            'discount_amount' => $grn->discount_amount ?? '',
            'is_percentage' => $grn->is_percentage,
            'items' => $grn->items->map(function ($item) {
                return [
                    'id' => $item->id ?? '',
                    'grn_id' => $item->grn_id ?? '',
                    'item_id' => $item->item_id ?? '',
                    'item_name' => $item->item_name ?? '',
                    'qty' => $item->qty ?? '',
                    'foc' => $item->foc ?? '',
                    'price' => $item->price ?? '',
                    'margin' => $item->margin ?? '',
                    'discount' => $item->discount ?? '',
                    'final_price' => $item->final_price ?? '',
                    'subtotal' => $item->subtotal ?? '',
                ];
            }),
        ];
    }
}
