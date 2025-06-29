<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\UseCases\Employee\DeleteEmployeeInteractor;
use App\UseCases\Employee\ListEmployeeInteractor;
use App\UseCases\Employee\Requests\EmployeeRequest;
use App\UseCases\Employee\ShowEmployeeInteractor;
use App\UseCases\Employee\StoreEmployeeInteractor;
use App\UseCases\Employee\updateEmployeeInteractor;

class EmployeeController extends Controller
{
    public function index(ListEmployeeInteractor $listEmployeeInteractor)
    {
        return $listEmployeeInteractor->execute();
    }

    public function store(StoreEmployeeInteractor $storeEmployeeInteractor)
    {
        $newEmployee = $storeEmployeeInteractor->execute(EmployeeRequest::validateAndCreate(request()));
        return response()->json($newEmployee , 201);
    }

    public function show(Employee $employee, ShowEmployeeInteractor $showEmployeeInteractor)
    {
        return $showEmployeeInteractor->execute($employee);
    }

    public function update(Employee $employee, UpdateEmployeeInteractor $updateEmployeeInteractor)
    {
        $updateEmployee = $updateEmployeeInteractor->execute($employee, EmployeeRequest::validateAndCreate(request()));
        return response()->json($updateEmployee);
    }

    public function destroy(Employee $employee, DeleteEmployeeInteractor $deleteEmployeeInteractor)
    {
        $deleteEmployeeInteractor->execute($employee);
        return response()->json(null, 204);
    }
}
