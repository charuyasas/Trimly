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
            'grn_type' => $grn->grn_type,
            'store_location' => $grn->store_location,
            'note' => $grn->note,
            'discount_amount' => $grn->discount_amount,
            'is_percentage' => $grn->is_percentage,
            'items' => $grn->items->makeHidden(['created_at', 'updated_at'])
        ];
    }
}
