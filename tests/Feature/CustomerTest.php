<?php

use App\Models\Customer;
use Illuminate\Support\Str;

test('customer can be created with valid data', function () {
    $customerData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'address' => '123 Main St'
    ];

    $customer = Customer::create($customerData);

    expect($customer)->toBeInstanceOf(Customer::class)
        ->and($customer->name)->toBe('John Doe')
        ->and($customer->email)->toBe('john@example.com')
        ->and($customer->phone)->toBe('1234567890')
        ->and($customer->address)->toBe('123 Main St')
        ->and($customer->id)->toBeString()
        ->and(Str::isUuid($customer->id))->toBeTrue();
});

test('customer id is automatically generated as uuid', function () {
    $customer = Customer::create([
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone' => '0987654321',
        'address' => '456 Oak Ave'
    ]);

    expect($customer->id)->toBeString()
        ->and(Str::isUuid($customer->id))->toBeTrue();
});

test('customer can be found by id', function () {
    $customer = Customer::create([
        'name' => 'Bob Wilson',
        'email' => 'bob@example.com',
        'phone' => '1122334455',
        'address' => '789 Pine Rd'
    ]);

    $foundCustomer = Customer::find($customer->id);

    expect($foundCustomer)->toBeInstanceOf(Customer::class)
        ->and($foundCustomer->id)->toBe($customer->id)
        ->and($foundCustomer->name)->toBe('Bob Wilson');
});

test('customer can be updated', function () {
    $customer = Customer::create([
        'name' => 'Alice Johnson',
        'email' => 'alice@example.com',
        'phone' => '1555666777',
        'address' => '321 Elm St'
    ]);

    $customer->update([
        'name' => 'Alice Smith',
        'phone' => '1888999000'
    ]);

    $updatedCustomer = Customer::find($customer->id);

    expect($updatedCustomer->name)->toBe('Alice Smith')
        ->and($updatedCustomer->phone)->toBe('1888999000')
        ->and($updatedCustomer->email)->toBe('alice@example.com'); // unchanged
});

test('customer can be soft deleted', function () {
    $customer = Customer::create([
        'name' => 'Charlie Brown',
        'email' => 'charlie@example.com',
        'phone' => '1444333222',
        'address' => '654 Maple Dr'
    ]);

    $customerId = $customer->id;
    $customer->delete();

    // Should not be found in normal queries
    expect(Customer::find($customerId))->toBeNull();

    // Should be found in withTrashed queries
    expect(Customer::withTrashed()->find($customerId))->not->toBeNull();
}); 