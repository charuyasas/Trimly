<?php
namespace App\UseCases\Employee;

use App\Models\Employee;

 class ListEmployeeIntractor {

    public function execute(){
        return Employee::all();
    }

 }