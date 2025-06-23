<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        return Employee::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:employees,employee_id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_no' => 'nullable|numeric',
        ]);

        $employee = Employee::create($validated);
        return response()->json($employee, 201);
    }

    public function show(string $id)
    {
        return Employee::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
       $validated = $request->validate([
            'employee_id' => [
                'required',
                'string',
                Rule::unique('employees', 'employee_id')->ignore($id),
            ],
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_no' => 'nullable|numeric',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->update($validated);
        return response()->json($employee);
    }

    public function destroy(string $id)
    {
        Employee::destroy($id);
        return response()->json(null, 204);
    }
}
