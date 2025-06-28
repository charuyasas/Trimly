<?php
namespace App\UseCases\Employee;

use App\Models\Employee;

 class ShowEmployeeInteractor {

    public function execute(Employee $employee){
        return $employee;
    }

 }