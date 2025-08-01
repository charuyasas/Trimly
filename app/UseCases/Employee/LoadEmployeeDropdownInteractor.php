<?php

namespace App\UseCases\Employee;

use App\Models\Employee;

class LoadEmployeeDropdownInteractor
{
    public function execute($search)
    {
        return Employee::where('employee_id', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%")
                        ->limit(10)
                        ->orderBy('employee_id', 'asc')
                        ->get()
            ->map(fn($employee) => [
                'employee_ledger_code' => $employee->ledger_code,
                'label' => $employee->employee_id . ' - ' . $employee->name,
                'value' => $employee->id
            ]);
    }
}
