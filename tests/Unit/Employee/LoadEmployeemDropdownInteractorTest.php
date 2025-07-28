<?php

use App\Models\Employee;
use App\UseCases\Employee\LoadEmployeeDropdownInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new LoadEmployeeDropdownInteractor();
});

test('returns all employees for dropdown', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith'
    ]);

    $result = $this->interactor->execute('');

    expect($result)->toHaveCount(2);
    expect($result[0])->toBeArray();
    expect($result[1])->toBeArray();
    expect($result[0]['label'])->toContain('John Doe');
    expect($result[1]['label'])->toContain('Jane Smith');
});

test('returns empty collection when no employees exist', function () {
    $result = $this->interactor->execute('');

    expect($result)->toBeEmpty();
    expect($result)->toHaveCount(0);
});

test('returns employees filtered by employee_id', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith'
    ]);

    $result = $this->interactor->execute('EMP001');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toContain('EMP001');
    expect($result[0]['label'])->toContain('John Doe');
});

test('returns employees filtered by name', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith'
    ]);

    $result = $this->interactor->execute('John');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toContain('EMP001');
    expect($result[0]['label'])->toContain('John Doe');
});

test('returns employees with partial name match', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP003',
        'name' => 'Johnny Appleseed'
    ]);

    $result = $this->interactor->execute('John');

    expect($result)->toHaveCount(2);
    expect($result->pluck('label')->toArray())->toContain('EMP001 - John Doe');
    expect($result->pluck('label')->toArray())->toContain('EMP003 - Johnny Appleseed');
});

test('returns employees with special characters in name', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'José María O\'Connor-Smith'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith'
    ]);

    $result = $this->interactor->execute('José');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toContain('José María O\'Connor-Smith');
});

test('returns employees with international characters', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => '李小明'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith'
    ]);

    $result = $this->interactor->execute('李');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toContain('李小明');
});

test('returns employees with alphanumeric employee_id', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP-001-A',
        'name' => 'Alphanumeric Employee'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Regular Employee'
    ]);

    $result = $this->interactor->execute('EMP-001');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toContain('EMP-001-A');
    expect($result[0]['label'])->toContain('Alphanumeric Employee');
});

test('limits results to 10 items', function () {
    // Create 12 employees
    for ($i = 1; $i <= 12; $i++) {
        Employee::factory()->create([
            'employee_id' => sprintf("EMP%03d", $i),
            'name' => "Employee {$i}"
        ]);
    }

    $result = $this->interactor->execute('');

    expect($result)->toHaveCount(10);
});

test('returns employees ordered by name', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP003',
        'name' => 'Charlie Brown'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Alice Johnson'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Bob Smith'
    ]);

    $result = $this->interactor->execute('');

    expect($result)->toHaveCount(3);
    expect($result[0]['label'])->toContain('Alice Johnson');
    expect($result[1]['label'])->toContain('Bob Smith');
    expect($result[2]['label'])->toContain('Charlie Brown');
});

test('returns employees with case insensitive search', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith'
    ]);

    $result = $this->interactor->execute('john');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toContain('John Doe');
});

test('returns employees with mixed search criteria', function () {
    Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Doe'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Jane Smith'
    ]);

    Employee::factory()->create([
        'employee_id' => 'EMP003',
        'name' => 'Bob Johnson'
    ]);

    $result = $this->interactor->execute('EMP');

    expect($result)->toHaveCount(3);
    expect($result->pluck('label')->toArray())->toContain('EMP001 - John Doe');
    expect($result->pluck('label')->toArray())->toContain('EMP002 - Jane Smith');
    expect($result->pluck('label')->toArray())->toContain('EMP003 - Bob Johnson');
});
