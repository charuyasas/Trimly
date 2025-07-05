<?php

use App\Models\Employee;
use App\UseCases\Employee\UpdateEmployeeInteractor;
use App\UseCases\Employee\Requests\EmployeeRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new UpdateEmployeeInteractor();
});

test('updates employee successfully', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $updateData = EmployeeRequest::from([
        'id' => $employee->id,
        'employee_id' => 'EMP001',
        'name' => 'John Smith',
        'address' => '456 Oak Ave',
        'contact_no' => '0987654321',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee, $updateData);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->name)->toBe('John Smith');
    expect($result->address)->toBe('456 Oak Ave');
    expect($result->contact_no)->toBe('0987654321');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'name' => 'John Smith',
        'address' => '456 Oak Ave',
        'contact_no' => '0987654321'
    ]);
});

test('updates employee with special characters', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $updateData = EmployeeRequest::from([
        'id' => $employee->id,
        'employee_id' => 'EMP001',
        'name' => 'José María O\'Connor-Smith',
        'address' => 'Calle Principal 123, Ciudad',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee, $updateData);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->name)->toBe('José María O\'Connor-Smith');
    expect($result->address)->toBe('Calle Principal 123, Ciudad');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'name' => 'José María O\'Connor-Smith',
        'address' => 'Calle Principal 123, Ciudad'
    ]);
});

test('updates employee with international characters', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $updateData = EmployeeRequest::from([
        'id' => $employee->id,
        'employee_id' => 'EMP001',
        'name' => '李小明',
        'address' => '北京路123号',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee, $updateData);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->name)->toBe('李小明');
    expect($result->address)->toBe('北京路123号');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'name' => '李小明',
        'address' => '北京路123号'
    ]);
});

test('updates employee with max length fields', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $name = str_repeat('a', 255);
    $address = str_repeat('b', 255);

    $updateData = EmployeeRequest::from([
        'id' => $employee->id,
        'employee_id' => 'EMP001',
        'name' => $name,
        'address' => $address,
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee, $updateData);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->name)->toBe($name);
    expect($result->address)->toBe($address);

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'name' => $name,
        'address' => $address
    ]);
});

test('updates employee with different phone format', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $updateData = EmployeeRequest::from([
        'id' => $employee->id,
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '+1-555-123-4567',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee, $updateData);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->contact_no)->toBe('+1-555-123-4567');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'contact_no' => '+1-555-123-4567'
    ]);
});

test('updates employee with different ledger code format', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $updateData = EmployeeRequest::from([
        'id' => $employee->id,
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'LEDGER-001'
    ]);

    $result = $this->interactor->execute($employee, $updateData);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->ledger_code)->toBe('LEDGER-001');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'ledger_code' => 'LEDGER-001'
    ]);
});

test('updates employee with alphanumeric employee_id', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $updateData = EmployeeRequest::from([
        'id' => $employee->id,
        'employee_id' => 'EMP-001-A',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP-001-A'
    ]);

    $result = $this->interactor->execute($employee, $updateData);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->employee_id)->toBe('EMP-001-A');
    expect($result->ledger_code)->toBe('EMP-001-A');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP-001-A',
        'ledger_code' => 'EMP-001-A'
    ]);
});

test('updates employee with long address', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $longAddress = '12345 Very Long Street Name, Apartment Building Complex, Suite 1000, Floor 10, Building A, City Center District, Metropolitan Area, State 12345-6789, Country';

    $updateData = EmployeeRequest::from([
        'id' => $employee->id,
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => $longAddress,
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee, $updateData);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->address)->toBe($longAddress);

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'address' => $longAddress
    ]);
});

test('updates employee and preserves other data', function () {
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

    $updateData = EmployeeRequest::from([
        'id' => $employee1->id,
        'employee_id' => 'EMP001',
        'name' => 'John Smith',
        'address' => '789 Pine St',
        'contact_no' => '5555555555',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee1, $updateData);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->name)->toBe('John Smith');

    // First employee should be updated
    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'name' => 'John Smith',
        'address' => '789 Pine St',
        'contact_no' => '5555555555'
    ]);

    // Second employee should remain unchanged
    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith',
        'address' => '456 Oak Ave',
        'contact_no' => '0987654321'
    ]);
});

test('updates employee with empty address', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $updateData = EmployeeRequest::from([
        'id' => $employee->id,
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee, $updateData);

    expect($result)->toBeInstanceOf(Employee::class);
    expect($result->address)->toBe('');

    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP001',
        'address' => ''
    ]);
}); 