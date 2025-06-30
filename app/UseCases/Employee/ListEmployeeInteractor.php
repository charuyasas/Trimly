<?php
namespace App\UseCases\Employee;

use App\Models\Employee;

 class ListEmployeeInteractor {

    public function execute(){
        return Employee::all();
    }

 }