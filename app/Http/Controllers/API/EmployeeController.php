<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\UseCases\Employee\DeleteEmployeeInteractor;
use App\UseCases\Employee\ListEmployeeInteractor;
use App\UseCases\Employee\LoadEmployeemDropdownInteractor;
use App\UseCases\Employee\Requests\EmployeeRequest;
use App\UseCases\Employee\ShowEmployeeInteractor;
use App\UseCases\Employee\StoreEmployeeInteractor;
use App\UseCases\Employee\updateEmployeeInteractor;
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;
use App\UseCases\PostingAccount\StorePostingAccountInteractor;

class EmployeeController extends Controller
{
    public function index(ListEmployeeInteractor $listEmployeeInteractor)
    {
        return $listEmployeeInteractor->execute();
    }

    public function store(StoreEmployeeInteractor $storeEmployeeInteractor, StorePostingAccountInteractor $storePostingAccountInteractor)
    {
        $ledgerCode = $this->createLedgerCode($storePostingAccountInteractor);
        $employeeData = array_merge(
            request()->all(),
            ['ledger_code' => $ledgerCode]
        );

        $newEmployee = $storeEmployeeInteractor->execute(EmployeeRequest::validateAndCreate($employeeData));
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

    public function loadEmployeeDropdown(LoadEmployeemDropdownInteractor $loadEmployeemDropdownInteractor)
    {
        return response()->json($loadEmployeemDropdownInteractor->execute(request('search_key')));
    }

    public function createLedgerCode(StorePostingAccountInteractor $storePostingAccountInteractor)
    {
        $data = [
            'posting_code'     => null,
            'posting_account'  => 'Employee Account',
            'main_code'        => 4,
            'heading_code'     => 8,
            'title_code'       => 9,
        ];

        $newPostingAccount = $storePostingAccountInteractor->execute(PostingAccountRequest::validateAndCreate($data));
        return $newPostingAccount['ledger_code'];
    }


}
