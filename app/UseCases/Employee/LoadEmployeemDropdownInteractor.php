<?php

namespace App\UseCases\Employee;

use App\Models\Employee;

class LoadEmployeemDropdownInteractor
{
    public function execute($search)
    {
        return Employee::where('employee_id', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%")
                        ->limit(10)
                        ->orderBy('employee_id', 'asc')
                        ->get()
            ->map(fn($employee) => [
                'label' => $employee->employee_id . ' - ' . $employee->name,
                'value' => $employee->id
            ]);
    }
}
