<?php
namespace App\UseCases\Employee;

use App\Models\Employee;

 class DeleteEmployeeInteractor {

    public function execute(Employee $employee){
        return $employee->delete();
    }

 }