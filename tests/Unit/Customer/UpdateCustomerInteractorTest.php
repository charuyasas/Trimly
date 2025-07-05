<?php

use App\Models\Customer;
use App\UseCases\Customer\UpdateCustomerInteractor;
use App\UseCases\Customer\Requests\CustomerRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new UpdateCustomerInteractor();
});

test('updates customer successfully', function () {
    $customer = Customer::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'phone' => '1234567890',
        'address' => 'Original Address'
    ]);

    $updateData = CustomerRequest::from([
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'phone' => '1234567890',
        'address' => 'Updated Address'
    ]);

    $result = $this->interactor->execute($customer, $updateData);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->id)->toBe($customer->id);
    expect($result->name)->toBe('Updated Name');
    expect($result->email)->toBe('updated@example.com');
    expect($result->phone)->toBe('1234567890');
    expect($result->address)->toBe('Updated Address');

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'phone' => '1234567890',
        'address' => 'Updated Address'
    ]);
});

test('updates customer with partial data', function () {
    $customer = Customer::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'phone' => '1234567890',
        'address' => 'Original Address'
    ]);

    // Update only name and email
    $updateData = CustomerRequest::from([
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'phone' => '1234567890', // Keep original
        'address' => 'Original Address' // Keep original
    ]);

    $result = $this->interactor->execute($customer, $updateData);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->name)->toBe('Updated Name');
    expect($result->email)->toBe('updated@example.com');
    expect($result->phone)->toBe('1234567890');
    expect($result->address)->toBe('Original Address');

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com'
    ]);
});

test('updates customer address to null', function () {
    $customer = Customer::factory()->create([
        'name' => 'Customer With Address',
        'email' => 'customer@example.com',
        'phone' => '1234567890',
        'address' => 'Original Address'
    ]);

    $updateData = CustomerRequest::from([
        'name' => 'Customer With Address',
        'email' => 'customer@example.com',
        'phone' => '1234567890',
        'address' => null
    ]);

    $result = $this->interactor->execute($customer, $updateData);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->address)->toBeNull();

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'address' => null
    ]);
});

test('updates customer with special characters', function () {
    $customer = Customer::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'phone' => '1234567890',
        'address' => 'Original Address'
    ]);

    $updateData = CustomerRequest::from([
        'name' => 'José María O\'Connor-Smith',
        'email' => 'jose@example.com',
        'phone' => '1234567890',
        'address' => 'Calle Principal 123, Ciudad'
    ]);

    $result = $this->interactor->execute($customer, $updateData);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->name)->toBe('José María O\'Connor-Smith');
    expect($result->email)->toBe('jose@example.com');
    expect($result->address)->toBe('Calle Principal 123, Ciudad');

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'José María O\'Connor-Smith',
        'email' => 'jose@example.com'
    ]);
});

test('updates customer with international characters', function () {
    $customer = Customer::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'phone' => '1234567890',
        'address' => 'Original Address'
    ]);

    $updateData = CustomerRequest::from([
        'name' => '李小明',
        'email' => 'li@example.com',
        'phone' => '1234567890',
        'address' => '北京路123号'
    ]);

    $result = $this->interactor->execute($customer, $updateData);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->name)->toBe('李小明');
    expect($result->email)->toBe('li@example.com');
    expect($result->address)->toBe('北京路123号');

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => '李小明',
        'email' => 'li@example.com'
    ]);
});

test('updates customer with long address', function () {
    $customer = Customer::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'phone' => '1234567890',
        'address' => 'Original Address'
    ]);

    $longAddress = '12345 Very Long Street Name, Apartment Building Complex, Suite 1000, Floor 10, Building A, City Center District, Metropolitan Area, State 12345-6789, Country';

    $updateData = CustomerRequest::from([
        'name' => 'Long Address Customer',
        'email' => 'longaddress@example.com',
        'phone' => '1234567890',
        'address' => $longAddress
    ]);

    $result = $this->interactor->execute($customer, $updateData);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->name)->toBe('Long Address Customer');
    expect($result->email)->toBe('longaddress@example.com');
    expect($result->address)->toBe($longAddress);

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Long Address Customer',
        'address' => $longAddress
    ]);
});

/*
test('updates customer with different phone format', function () {
    $customer = Customer::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'phone' => '1234567890',
        'address' => 'Original Address'
    ]);

    $updateData = CustomerRequest::from([
        'name' => 'Phone Test Customer',
        'email' => 'phone@example.com',
        'phone' => '+1-555-123-4567',
        'address' => '123 Phone St'
    ]);

    $result = $this->interactor->execute($customer, $updateData);

    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->name)->toBe('Phone Test Customer');
    expect($result->email)->toBe('phone@example.com');
    expect($result->phone)->toBe('+1-555-123-4567');

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'phone' => '+1-555-123-4567'
    ]);
});
*/ 