<?php

use App\Models\Employee;
use App\UseCases\Employee\DeleteEmployeeInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new DeleteEmployeeInteractor();
});

test('deletes employee successfully', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe',
        'address' => '123 Main St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeTrue();

    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP001',
        'name' => 'John Doe'
    ]);
});

test('deletes employee with special characters in name', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'José María O\'Connor-Smith',
        'address' => 'Calle Principal 123, Ciudad',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP002'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeTrue();

    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP002',
        'name' => 'José María O\'Connor-Smith'
    ]);
});

test('deletes employee with different phone format', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP003',
        'name' => 'Phone Test Employee',
        'address' => '123 Phone St',
        'contact_no' => '+1-555-123-4567',
        'ledger_code' => 'EMP003'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeTrue();

    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP003',
        'contact_no' => '+1-555-123-4567'
    ]);
});

test('deletes employee with different ledger code format', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP004',
        'name' => 'Ledger Test Employee',
        'address' => '123 Ledger St',
        'contact_no' => '1234567890',
        'ledger_code' => 'LEDGER-004'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeTrue();

    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP004',
        'ledger_code' => 'LEDGER-004'
    ]);
});

test('deletes multiple employees', function () {
    $employee1 = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Employee One'
    ]);

    $employee2 = Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Employee Two'
    ]);

    $result1 = $this->interactor->execute($employee1);
    $result2 = $this->interactor->execute($employee2);

    expect($result1)->toBeTrue();
    expect($result2)->toBeTrue();

    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP001',
        'name' => 'Employee One'
    ]);

    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP002',
        'name' => 'Employee Two'
    ]);
});

test('deletes employee with alphanumeric employee_id', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP-001-A',
        'name' => 'Alphanumeric Employee',
        'address' => '123 Alphanumeric St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP-001-A'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeTrue();

    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP-001-A',
        'name' => 'Alphanumeric Employee'
    ]);
});

test('deletes employee with long address', function () {
    $longAddress = '12345 Very Long Street Name, Apartment Building Complex, Suite 1000, Floor 10, Building A, City Center District, Metropolitan Area, State 12345-6789, Country';
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP005',
        'name' => 'Long Address Employee',
        'address' => $longAddress,
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP005'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeTrue();

    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP005',
        'address' => $longAddress
    ]);
});

test('deletes employee with international characters', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP006',
        'name' => '李小明',
        'address' => '北京路123号',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP006'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeTrue();

    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP006',
        'name' => '李小明',
        'address' => '北京路123号'
    ]);
});

test('deletes employee with numeric posting code', function () {
    $employee = Employee::factory()->create([
        'employee_id' => 'EMP007',
        'name' => 'Numeric Code Employee',
        'address' => '123 Numeric St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP007'
    ]);

    $result = $this->interactor->execute($employee);

    expect($result)->toBeTrue();

    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP007',
        'name' => 'Numeric Code Employee'
    ]);
});

test('deletes employee and preserves other data', function () {
    $employee1 = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Employee One'
    ]);

    $employee2 = Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Employee Two'
    ]);

    $result = $this->interactor->execute($employee1);

    expect($result)->toBeTrue();

    // First employee should be deleted
    $this->assertSoftDeleted('employees', [
        'employee_id' => 'EMP001',
        'name' => 'Employee One'
    ]);

    // Second employee should still exist
    $this->assertDatabaseHas('employees', [
        'employee_id' => 'EMP002',
        'name' => 'Employee Two'
    ]);
}); 