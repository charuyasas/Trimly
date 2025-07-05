<?php

use App\Models\Employee;
use App\UseCases\Employee\ListEmployeeInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new ListEmployeeInteractor();
});

test('returns all employees', function () {
    $employee1 = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $employee2 = Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith',
        'address' => '456 Oak Ave',
        'contact_no' => '0987654321',
        'ledger_code' => 'EMP002'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(2);
    expect($result[0])->toBeInstanceOf(Employee::class);
    expect($result[1])->toBeInstanceOf(Employee::class);
    expect($result->pluck('name')->toArray())->toContain('John Doe');
    expect($result->pluck('name')->toArray())->toContain('Jane Smith');
});

test('returns empty collection when no employees exist', function () {
    $result = $this->interactor->execute();

    expect($result)->toBeEmpty();
    expect($result)->toHaveCount(0);
});

test('returns employees ordered by name', function () {
    $employee3 = Employee::factory()->create([
        'employee_id' => 'EMP003',
        'name' => 'Charlie Brown',
        'contact_no' => '3333333333',
        'ledger_code' => 'EMP003'
    ]);

    $employee1 = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Alice Johnson',
        'contact_no' => '1111111111',
        'ledger_code' => 'EMP001'
    ]);

    $employee2 = Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Bob Wilson',
        'contact_no' => '2222222222',
        'ledger_code' => 'EMP002'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(3);
    expect($result->pluck('name')->toArray())->toContain('Alice Johnson');
    expect($result->pluck('name')->toArray())->toContain('Bob Wilson');
    expect($result->pluck('name')->toArray())->toContain('Charlie Brown');
});

test('returns employees with all fields populated', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Complete Employee',
        'address' => '789 Complete Blvd, Suite 100, City, State 12345',
        'contact_no' => '5555555555',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(1);
    expect($result[0]->employee_id)->toBe('EMP001');
    expect($result[0]->name)->toBe('Complete Employee');
    expect($result[0]->address)->toBe('789 Complete Blvd, Suite 100, City, State 12345');
    expect($result[0]->contact_no)->toBe('5555555555');
    expect($result[0]->ledger_code)->toBe('EMP001');
});

test('returns employees with null address', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'No Address Employee',
        'address' => null,
        'contact_no' => '6666666666',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(1);
    expect($result[0]->name)->toBe('No Address Employee');
    expect($result[0]->address)->toBeNull();
});

test('returns multiple employees with different data combinations', function () {
    $employee1 = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Employee One',
        'address' => 'Address One',
        'contact_no' => '1111111111',
        'ledger_code' => 'EMP001'
    ]);

    $employee2 = Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Employee Two',
        'address' => null,
        'contact_no' => '2222222222',
        'ledger_code' => 'EMP002'
    ]);

    $employee3 = Employee::factory()->create([
        'employee_id' => 'EMP003',
        'name' => 'Employee Three',
        'address' => 'Address Three',
        'contact_no' => '3333333333',
        'ledger_code' => 'EMP003'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(3);
    expect($result->pluck('name')->toArray())->toContain('Employee One');
    expect($result->pluck('name')->toArray())->toContain('Employee Two');
    expect($result->pluck('name')->toArray())->toContain('Employee Three');
    expect($result->pluck('employee_id')->toArray())->toContain('EMP001');
    expect($result->pluck('employee_id')->toArray())->toContain('EMP002');
    expect($result->pluck('employee_id')->toArray())->toContain('EMP003');
});

test('returns employees with special characters in names', function () {
    $employee1 = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'José María',
        'contact_no' => '1111111111',
        'ledger_code' => 'EMP001'
    ]);

    $employee2 = Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'O\'Connor-Smith',
        'contact_no' => '2222222222',
        'ledger_code' => 'EMP002'
    ]);

    $employee3 = Employee::factory()->create([
        'employee_id' => 'EMP003',
        'name' => '李小明',
        'contact_no' => '3333333333',
        'ledger_code' => 'EMP003'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(3);
    expect($result->pluck('name')->toArray())->toContain('José María');
    expect($result->pluck('name')->toArray())->toContain('O\'Connor-Smith');
    expect($result->pluck('name')->toArray())->toContain('李小明');
});

test('returns employees with different ledger code formats', function () {
    $employee1 = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Employee One',
        'contact_no' => '1111111111',
        'ledger_code' => 'EMP001'
    ]);

    $employee2 = Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Employee Two',
        'contact_no' => '2222222222',
        'ledger_code' => 'LEDGER-002'
    ]);

    $employee3 = Employee::factory()->create([
        'employee_id' => 'EMP003',
        'name' => 'Employee Three',
        'contact_no' => '3333333333',
        'ledger_code' => 'EMP_003'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(3);
    expect($result->pluck('ledger_code')->toArray())->toContain('EMP001');
    expect($result->pluck('ledger_code')->toArray())->toContain('LEDGER-002');
    expect($result->pluck('ledger_code')->toArray())->toContain('EMP_003');
}); 