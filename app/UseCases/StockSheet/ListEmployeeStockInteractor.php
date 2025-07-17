<?php

namespace App\UseCases\StockSheet;

use App\Models\StockSheet;
use Illuminate\Support\Str;

class ListEmployeeStockInteractor
{
    public function execute()
    {
        return StockSheet::with('employee')
            ->where('reference_type', 'Employee Issue')
            ->latest()
            ->limit(100)
            ->get()
            ->groupBy('reference_id')
            ->map(function ($group, $referenceId) {
                $withEmployee = $group->firstWhere(fn ($item) => $item->employee !== null);
                $referenceNumber = Str::after($referenceId, 'Employee Issue - ');
                $firstItem = $group->first();

                return [
                    'reference_id'   => $referenceNumber,
                    'employee_code'  => $withEmployee?->employee->employee_id ?? 'N/A',
                    'employee_name'  => $withEmployee?->employee->name ?? 'N/A',
                    'created_at'     => $firstItem->created_at->toDateString(),
                ];
            })->values();

    }
}
