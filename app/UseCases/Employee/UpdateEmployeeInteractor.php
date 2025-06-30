<?php

namespace App\UseCases\Employee;

use App\Models\Employee;
use App\UseCases\Employee\Requests\EmployeeRequest;

 class UpdateEmployeeInteractor {

    public function execute(Employee $employee, EmployeeRequest $employeeRequest){
        $employee->update($employeeRequest->except('id')->toArray());

        return $employee;
    }

 }