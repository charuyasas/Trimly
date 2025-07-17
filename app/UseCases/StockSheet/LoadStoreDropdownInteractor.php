<?php

namespace App\UseCases\StockSheet;

use App\Models\Employee;

class LoadStoreDropdownInteractor
{
    public function execute($search)
    {
        return Employee::where('employee_id', 'like', "%$search%")
            ->orWhere('name', 'like', "%$search%")
            ->limit(10)
            ->orderBy('employee_id', 'asc')
            ->get()
            ->map(fn($employee) => [
                'store_ledger_code' => $employee->ledger_code,
                'label' => $employee->employee_id . ' - ' . $employee->name,
                'value' => $employee->id
            ])
            ->push([
                'store_ledger_code' => '1-2-6-1000',
                'label' => 'Main Store',
                'value' => '1000'  // or a custom identifier like 'main-store'
            ]);
    }
}
