<?php

namespace App\UseCases\StockSheet;

use App\Models\Employee;
use App\Models\StockSheet;
use Illuminate\Support\Str;

class ListItemIssueInteractor
{
    public function execute()
    {
        // Employee Issue Summary (grouped by reference_id)
        $employeeIssues = StockSheet::with('employee') // relation for credit side (receiver)
        ->where('reference_type', StockSheet::STATUS['Employee Issue'])
            ->latest()
            ->limit(100)
            ->get()
            ->groupBy('reference_id')
            ->map(function ($group, $referenceId) {

                $firstItem = $group->first();
                $referenceNumber = Str::after($referenceId, 'Employee Issue - ');

                // Credit side: who received stock
                $creditEntry = $group->firstWhere(fn($item) => $item->credit > 0);
                $creditEmployee = $creditEntry?->employee;

                // Debit side: who issued stock
                $debitEntry = $group->firstWhere(fn($item) => $item->debit > 0);
                $debitLedger = $debitEntry?->ledger_code;

                if ($debitLedger === '1-2-6-1000') {
                    $issuedFrom = 'Main Store';
                    $issuedFromCode = $debitLedger;
                } else {
                    $storeEmployee = Employee::where('ledger_code', $debitLedger)->first();
                    $issuedFrom = $storeEmployee?->name ?? 'Unknown';
                    $issuedFromCode = $storeEmployee?->employee_id ?? $debitLedger;
                }

                return [
                    'reference_id'     => $referenceNumber,
                    'issued_store_code' => $issuedFromCode,
                    'issued_store'      => $issuedFrom,
                    'employee_code'    => $creditEmployee?->employee_id ?? 'N/A',
                    'employee_name'    => $creditEmployee?->name ?? 'N/A',
                    'created_at'       => $firstItem->created_at->toDateString(),
                ];
            })->values();


        // Employee stock balances grouped by ledger_code
        $employeeStockBalances = Employee::with(['stockSheets.items'])->get()
            ->map(function ($employee) {
                return $employee->stockSheets
                    ->groupBy('item_code')
                    ->map(function ($entries) use ($employee) {
                        $credit = $entries->sum('credit');
                        $debit = $entries->sum('debit');
                        $currentStock = $credit - $debit;

                        $item = $entries->first()?->items;

                        return [
                            'employee_code' => $employee->employee_id,
                            'employee_name' => $employee->name,
                            'item_code'     => $item?->code,
                            'item_description' => $item?->description,
                            'current_stock' => $currentStock,
                        ];
                    });
            })
            ->flatten(1)
            ->filter(fn ($row) => $row['current_stock'] != 0)
            ->sortBy(['employee_code', 'item_code'])
            ->values();


        // Return both parts as array
        return [
            'issues'         => $employeeIssues,
            'stock_balances' => $employeeStockBalances,
        ];
    }

}
