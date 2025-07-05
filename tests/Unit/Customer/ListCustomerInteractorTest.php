<?php

use App\Models\Customer;
use App\UseCases\Customer\ListCustomerInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new ListCustomerInteractor();
});

test('returns all customers', function () {
    $customer1 = Customer::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'address' => '123 Main St'
    ]);

    $customer2 = Customer::factory()->create([
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone' => '0987654321',
        'address' => '456 Oak Ave'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(2);
    expect($result[0])->toBeInstanceOf(Customer::class);
    expect($result[1])->toBeInstanceOf(Customer::class);
    expect($result[0]->name)->toBe('John Doe');
    expect($result[1]->name)->toBe('Jane Smith');
});

test('returns empty collection when no customers exist', function () {
    $result = $this->interactor->execute();

    expect($result)->toBeEmpty();
    expect($result)->toHaveCount(0);
});

test('returns customers ordered by name', function () {
    $customer3 = Customer::factory()->create([
        'name' => 'Charlie Brown',
        'email' => 'charlie@example.com',
        'phone' => '3333333333'
    ]);

    $customer1 = Customer::factory()->create([
        'name' => 'Alice Johnson',
        'email' => 'alice@example.com',
        'phone' => '1111111111'
    ]);

    $customer2 = Customer::factory()->create([
        'name' => 'Bob Wilson',
        'email' => 'bob@example.com',
        'phone' => '2222222222'
    ]);

    $result = $this->interactor->execute();
    $names = collect($result)->pluck('name')->sort()->values()->toArray();
    expect($names)->toBe(['Alice Johnson', 'Bob Wilson', 'Charlie Brown']);
});

test('returns customers with all fields populated', function () {
    $customer = Customer::factory()->create([
        'name' => 'Complete Customer',
        'email' => 'complete@example.com',
        'phone' => '5555555555',
        'address' => '789 Complete Blvd, Suite 100, City, State 12345'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(1);
    expect($result[0]->name)->toBe('Complete Customer');
    expect($result[0]->email)->toBe('complete@example.com');
    expect($result[0]->phone)->toBe('5555555555');
    expect($result[0]->address)->toBe('789 Complete Blvd, Suite 100, City, State 12345');
});

test('returns customers with null address', function () {
    $customer = Customer::factory()->create([
        'name' => 'No Address Customer',
        'email' => 'noaddress@example.com',
        'phone' => '6666666666',
        'address' => null
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(1);
    expect($result[0]->name)->toBe('No Address Customer');
    expect($result[0]->address)->toBeNull();
});

test('returns multiple customers with different data combinations', function () {
    $customer1 = Customer::factory()->create([
        'name' => 'Customer One',
        'email' => 'customer1@example.com',
        'phone' => '1111111111',
        'address' => 'Address One'
    ]);

    $customer2 = Customer::factory()->create([
        'name' => 'Customer Two',
        'email' => 'customer2@example.com',
        'phone' => '2222222222',
        'address' => null
    ]);

    $customer3 = Customer::factory()->create([
        'name' => 'Customer Three',
        'email' => 'customer3@example.com',
        'phone' => '3333333333',
        'address' => 'Address Three'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(3);
    expect($result->pluck('name')->toArray())->toContain('Customer One');
    expect($result->pluck('name')->toArray())->toContain('Customer Two');
    expect($result->pluck('name')->toArray())->toContain('Customer Three');
    expect($result->pluck('email')->toArray())->toContain('customer1@example.com');
    expect($result->pluck('email')->toArray())->toContain('customer2@example.com');
    expect($result->pluck('email')->toArray())->toContain('customer3@example.com');
});

test('returns customers with special characters in names', function () {
    $customer1 = Customer::factory()->create([
        'name' => 'José María',
        'email' => 'jose@example.com',
        'phone' => '1111111111'
    ]);

    $customer2 = Customer::factory()->create([
        'name' => 'O\'Connor-Smith',
        'email' => 'oconnor@example.com',
        'phone' => '2222222222'
    ]);

    $customer3 = Customer::factory()->create([
        'name' => '李小明',
        'email' => 'li@example.com',
        'phone' => '3333333333'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(3);
    expect($result->pluck('name')->toArray())->toContain('José María');
    expect($result->pluck('name')->toArray())->toContain('O\'Connor-Smith');
    expect($result->pluck('name')->toArray())->toContain('李小明');
}); 