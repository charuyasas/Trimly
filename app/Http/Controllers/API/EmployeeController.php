<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Validation\Rule;
use App\UseCases\Employee\ListEmployeeIntractor;
use App\UseCases\Employee\Requests\EmployeeRequest;
use App\UseCases\Employee\StoreEmployeeInteractor;
use App\UseCases\Employee\updateEmployeeInteractor;

class EmployeeController extends Controller
{
    public function index(ListEmployeeIntractor $listEmployeeIntractor)
    {
        return $listEmployeeIntractor->execute();
    }

    public function store(StoreEmployeeInteractor $storeEmployeeInteractor)
    {
        $newEmployee = $storeEmployeeInteractor->execute(EmployeeRequest::validateAndCreate(request()));
        return response()->json($newEmployee , 201);
    }

    public function show(string $id)
    {
        return Employee::findOrFail($id);
    }

    public function update(Employee $employee, UpdateEmployeeInteractor $updateEmployeeInteractor)
    {
        $updateEmployee = $updateEmployeeInteractor->execute($employee, EmployeeRequest::validateAndCreate(request()));
        return response()->json($updateEmployee);
    }

    public function destroy(string $id)
    {
        Employee::destroy($id);
        return response()->json(null, 204);
    }
}
