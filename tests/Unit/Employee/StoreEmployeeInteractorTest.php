<?php

use App\Models\Employee;
use App\UseCases\Employee\StoreEmployeeInteractor;
use App\UseCases\Employee\Requests\EmployeeRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new StoreEmployeeInteractor();
});

test('creates a new employee successfully', function () {
    $employeeData = EmployeeRequest::from([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St, City, State 12345',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employeeData);

    expect($result)->toBeArray();
    expect($result['employee_id'])->toBe('EMP001');
    expect($result['name'])->toBe('John Doe');
    expect($result['address'])->toBe('123 Main St, City, State 12345');
    expect($result['contact_no'])->toBe('1234567890');
    expect($result['ledger_code'])->toBe('EMP001');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St, City, State 12345',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);
});

test('creates employee with minimal required data', function () {
    $employeeData = EmployeeRequest::from([
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith',
        'address' => '',
        'contact_no' => '0987654321',
        'ledger_code' => 'EMP002'
    ]);

    $result = $this->interactor->execute($employeeData);

    expect($result)->toBeArray();
    expect($result['employee_id'])->toBe('EMP002');
    expect($result['name'])->toBe('Jane Smith');
    expect($result['contact_no'])->toBe('0987654321');
    expect($result['ledger_code'])->toBe('EMP002');
    expect($result['address'])->toBe('');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith',
        'contact_no' => '0987654321',
        'ledger_code' => 'EMP002',
        'address' => ''
    ]);
});

test('creates employee with empty address', function () {
    $employeeData = EmployeeRequest::from([
        'employee_id' => 'EMP003',
        'name' => 'Bob Johnson',
        'address' => '',
        'contact_no' => '5555555555',
        'ledger_code' => 'EMP003'
    ]);

    $result = $this->interactor->execute($employeeData);

    expect($result)->toBeArray();
    expect($result['address'])->toBe('');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP003',
        'name' => 'Bob Johnson',
        'address' => '',
        'contact_no' => '5555555555',
        'ledger_code' => 'EMP003'
    ]);
});

test('creates multiple employees successfully', function () {
    $employee1Data = EmployeeRequest::from([
        'employee_id' => 'EMP001',
        'name' => 'Employee One',
        'address' => 'Address One',
        'contact_no' => '1111111111',
        'ledger_code' => 'EMP001'
    ]);

    $employee2Data = EmployeeRequest::from([
        'employee_id' => 'EMP002',
        'name' => 'Employee Two',
        'address' => 'Address Two',
        'contact_no' => '2222222222',
        'ledger_code' => 'EMP002'
    ]);

    $result1 = $this->interactor->execute($employee1Data);
    $result2 = $this->interactor->execute($employee2Data);

    expect($result1)->toBeArray();
    expect($result2)->toBeArray();
    expect($result1['employee_id'])->not->toBe($result2['employee_id']);

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'name' => 'Employee One'
    ]);

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP002',
        'name' => 'Employee Two'
    ]);
});

test('creates employee with special characters in name', function () {
    $employeeData = EmployeeRequest::from([
        'employee_id' => 'EMP004',
        'name' => 'José María O\'Connor-Smith',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP004'
    ]);

    $result = $this->interactor->execute($employeeData);

    expect($result)->toBeArray();
    expect($result['name'])->toBe('José María O\'Connor-Smith');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP004',
        'name' => 'José María O\'Connor-Smith'
    ]);
});

test('creates employee with long address', function () {
    $longAddress = '12345 Very Long Street Name, Apartment Building Complex, Suite 1000, Floor 10, Building A, City Center District, Metropolitan Area, State 12345-6789, Country';

    $employeeData = EmployeeRequest::from([
        'employee_id' => 'EMP005',
        'name' => 'Long Address Employee',
        'address' => $longAddress,
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP005'
    ]);

    $result = $this->interactor->execute($employeeData);

    expect($result)->toBeArray();
    expect($result['address'])->toBe($longAddress);

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP005',
        'address' => $longAddress
    ]);
});

test('creates employee with different phone formats', function () {
    $employeeData = EmployeeRequest::from([
        'employee_id' => 'EMP006',
        'name' => 'Phone Test Employee',
        'address' => '123 Phone St',
        'contact_no' => '+1-555-123-4567',
        'ledger_code' => 'EMP006'
    ]);

    $result = $this->interactor->execute($employeeData);

    expect($result)->toBeArray();
    expect($result['contact_no'])->toBe('+1-555-123-4567');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP006',
        'contact_no' => '+1-555-123-4567'
    ]);
});

test('creates employee with different ledger code formats', function () {
    $employeeData = EmployeeRequest::from([
        'employee_id' => 'EMP007',
        'name' => 'Ledger Test Employee',
        'address' => '123 Ledger St',
        'contact_no' => '1234567890',
        'ledger_code' => 'LEDGER-007'
    ]);

    $result = $this->interactor->execute($employeeData);

    expect($result)->toBeArray();
    expect($result['ledger_code'])->toBe('LEDGER-007');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP007',
        'ledger_code' => 'LEDGER-007'
    ]);
}); 