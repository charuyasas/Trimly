<?php

use App\Models\Employee;
use App\UseCases\Employee\ShowEmployeeInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new ShowEmployeeInteractor();
});

test('returns employee when employee exists', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->id)->toBe($employee->id);
    expect($result->employee_id)->toBe('EMP001');
    expect($result->name)->toBe('John Doe');
    expect($result->address)->toBe('123 Main St');
    expect($result->contact_no)->toBe('1234567890');
    expect($result->ledger_code)->toBe('EMP001');
});

test('returns employee with special characters in name', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'José María O\'Connor-Smith',
        'address' => 'Calle Principal 123, Ciudad',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->id)->toBe($employee->id);
    expect($result->name)->toBe('José María O\'Connor-Smith');
    expect($result->address)->toBe('Calle Principal 123, Ciudad');
});

test('returns employee with international characters', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => '李小明',
        'address' => '北京路123号',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->id)->toBe($employee->id);
    expect($result->name)->toBe('李小明');
    expect($result->address)->toBe('北京路123号');
});

test('returns employee with max length fields', function () {
    $name = str_repeat('a', 255);
    $address = str_repeat('b', 255);
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => $name,
        'address' => $address,
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->id)->toBe($employee->id);
    expect($result->name)->toBe($name);
    expect($result->address)->toBe($address);
});

test('returns employee with different phone format', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Phone Test Employee',
        'address' => '123 Phone St',
        'contact_no' => '9876543210',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->id)->toBe($employee->id);
    expect($result->contact_no)->toBe('9876543210');
});

test('returns employee with different ledger code format', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Ledger Test Employee',
        'address' => '123 Ledger St',
        'contact_no' => '1234567890',
        'ledger_code' => 'LEDGER-001'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->id)->toBe($employee->id);
    expect($result->ledger_code)->toBe('LEDGER-001');
});

test('returns employee with alphanumeric employee_id', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP-001-A',
        'name' => 'Alphanumeric Employee',
        'address' => '123 Alphanumeric St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP-001-A'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->id)->toBe($employee->id);
    expect($result->employee_id)->toBe('EMP-001-A');
});

test('returns employee with long address', function () {
    $longAddress = '12345 Very Long Street Name, Apartment Building Complex, Suite 1000, Floor 10, Building A, City Center District, Metropolitan Area, State 12345-6789, Country';
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Long Address Employee',
        'address' => $longAddress,
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->id)->toBe($employee->id);
    expect($result->address)->toBe($longAddress);
}); 