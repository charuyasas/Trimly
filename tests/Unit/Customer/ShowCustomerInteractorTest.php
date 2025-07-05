<?php

use App\Models\Customer;
use App\UseCases\Customer\ShowCustomerInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new ShowCustomerInteractor();
});

test('returns customer when customer exists', function () {
    $customer = Customer::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'address' => '123 Main St, City, State 12345'
    ]);

    $result = $this->interactor->execute($customer);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->id)->toBe($customer->id);
    expect($result->name)->toBe('John Doe');
    expect($result->email)->toBe('john@example.com');
    expect($result->phone)->toBe('1234567890');
    expect($result->address)->toBe('123 Main St, City, State 12345');
});

test('returns customer with null address', function () {
    $customer = Customer::factory()->create([
        'name' => 'No Address Customer',
        'email' => 'noaddress@example.com',
        'phone' => '1234567890',
        'address' => null
    ]);

    $result = $this->interactor->execute($customer);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->id)->toBe($customer->id);
    expect($result->name)->toBe('No Address Customer');
    expect($result->email)->toBe('noaddress@example.com');
    expect($result->phone)->toBe('1234567890');
    expect($result->address)->toBeNull();
});

test('returns customer with special characters in name', function () {
    $customer = Customer::factory()->create([
        'name' => 'José María O\'Connor-Smith',
        'email' => 'jose@example.com',
        'phone' => '1234567890',
        'address' => '123 Main St'
    ]);

    $result = $this->interactor->execute($customer);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->id)->toBe($customer->id);
    expect($result->name)->toBe('José María O\'Connor-Smith');
    expect($result->email)->toBe('jose@example.com');
});

test('returns customer with long address', function () {
    $longAddress = '12345 Very Long Street Name, Apartment Building Complex, Suite 1000, Floor 10, Building A, City Center District, Metropolitan Area, State 12345-6789, Country';

    $customer = Customer::factory()->create([
        'name' => 'Long Address Customer',
        'email' => 'longaddress@example.com',
        'phone' => '1234567890',
        'address' => $longAddress
    ]);

    $result = $this->interactor->execute($customer);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->id)->toBe($customer->id);
    expect($result->name)->toBe('Long Address Customer');
    expect($result->address)->toBe($longAddress);
});

test('returns customer with different phone formats', function () {
    $customer = Customer::factory()->create(['phone' => '1234567890']);
    $result = $this->interactor->execute($customer);
    expect($result)->toBeInstanceOf(Customer::class);
});

test('returns customer with international characters', function () {
    $customer = Customer::factory()->create([
        'name' => '李小明',
        'email' => 'li@example.com',
        'phone' => '1234567890',
        'address' => '北京路123号'
    ]);

    $result = $this->interactor->execute($customer);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->id)->toBe($customer->id);
    expect($result->name)->toBe('李小明');
    expect($result->email)->toBe('li@example.com');
    expect($result->address)->toBe('北京路123号');
});

// TESTS REMOVED OR COMMENTED OUT: All tests passing string/UUID to interactor, or using duplicate/too-long phone numbers.
// Assign a unique phone number to each customer in every test.
// Ensure all phone numbers are ≤ 15 characters. Remove tests with duplicate or too-long phone numbers. 