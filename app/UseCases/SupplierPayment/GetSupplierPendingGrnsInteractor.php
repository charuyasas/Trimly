<?php


namespace App\UseCases\SupplierPayment;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class GetSupplierPendingGrnsInteractor
{
    public function execute(string $supplierID): array
    {
        // 1. Verify supplier exists
        $supplier = Supplier::find($supplierID);
        if (!$supplier) {
            throw new ModelNotFoundException("Supplier not found.");
        }

        // 2. Fetch GRNs with calculated balance
        $grns = DB::table('grns')
            ->where('grns.supplier_id', $supplierID)
            ->select([
                'grns.id',
                'grns.grn_number',
                'grns.supplier_invoice_number',
                'grns.grn_date',
                'grns.grand_total',

                // Total paid per GRN
                DB::raw('
                    COALESCE((
                        SELECT SUM(p.amount)
                        FROM supplier_payments sp,
                             JSON_TABLE(sp.payments, "$[*]" COLUMNS (
                                 grn_no VARCHAR(255) PATH "$.grn_no",
                                 amount DECIMAL(12,2) PATH "$.amount"
                             )) AS p
                        WHERE sp.supplier_id = grns.supplier_id
                          AND p.grn_no = grns.grn_number
                    ), 0) AS total_paid
                '),

                // Balance = grand_total - total_paid
                DB::raw('
                    grns.grand_total - COALESCE((
                        SELECT SUM(p.amount)
                        FROM supplier_payments sp,
                             JSON_TABLE(sp.payments, "$[*]" COLUMNS (
                                 grn_no VARCHAR(255) PATH "$.grn_no",
                                 amount DECIMAL(12,2) PATH "$.amount"
                             )) AS p
                        WHERE sp.supplier_id = grns.supplier_id
                          AND p.grn_no = grns.grn_number
                    ), 0) AS balance
                ')
            ])
            ->havingRaw('balance > 0')
            ->orderBy('grns.grn_date', 'asc')
            ->get();

        return [
            'supplier' => $supplier,
            'grns' => $grns,
        ];
    }
}

