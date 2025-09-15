<?php

namespace App\UseCases\SupplierPayment;

use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class ListSupplierPaymentInteractor
{
    public function execute()
    {
        return Supplier::query()
            ->select([
                'suppliers.id',
                'suppliers.supplier_code',
                'suppliers.name',
                DB::raw('COALESCE(SUM(journal_entries.credit), 0) as total_amount'),
                DB::raw('(COALESCE(SUM(journal_entries.credit), 0) - COALESCE(SUM(journal_entries.debit), 0)) as balance'),
            ])
            ->leftJoin('journal_entries', 'suppliers.ledger_code', '=', 'journal_entries.ledger_code')
            ->groupBy('suppliers.id', 'suppliers.supplier_code', 'suppliers.name')
            ->havingRaw('(COALESCE(SUM(journal_entries.credit), 0) - COALESCE(SUM(journal_entries.debit), 0)) > 0')
            ->get();
    }
}
