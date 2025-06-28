<?php

namespace App\UseCases\Employee;

use App\Models\Employee;
use App\UseCases\Employee\Requests\EmployeeRequest;


 class StoreEmployeeInteractor {

    public function execute(EmployeeRequest $employeeRequest){
        $employee = Employee::create($employeeRequest->toArray());
        return $employee->toArray();
    }

 }