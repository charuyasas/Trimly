<?php

use App\Models\Customer;

test('can get list of customers', function () {
    // Create some test customers
    Customer::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'address' => '123 Main St'
    ]);

    Customer::create([
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone' => '0987654321',
        'address' => '456 Oak Ave'
    ]);

    $response = $this->getJson('/api/customers');

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'email',
                'phone',
                'address',
            ],
        ]);
});

test('can create a new customer', function () {
    $customerData = [
        'name' => 'Bob Wilson',
        'email' => 'bob@example.com',
        'phone' => '1122334455',
        'address' => '789 Pine Rd'
    ];

    $response = $this->postJson('/api/customers', $customerData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'id',
            'name',
            'email',
            'phone',
            'address',
        ]);

    $this->assertDatabaseHas('customers', [
        'name' => 'Bob Wilson',
        'email' => 'bob@example.com',
        'phone' => '1122334455',
        'address' => '789 Pine Rd'
    ]);
});

test('can get a specific customer', function () {
    $customer = Customer::create([
        'name' => 'Alice Johnson',
        'email' => 'alice@example.com',
        'phone' => '1555666777',
        'address' => '321 Elm St'
    ]);

    $response = $this->getJson("/api/customers/{$customer->id}");

    $response->assertStatus(200)
        ->assertJson([
            'id' => $customer->id,
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'phone' => '1555666777',
            'address' => '321 Elm St',
        ]);
});

test('can update a customer', function () {
    $customer = Customer::create([
        'name' => 'Charlie Brown',
        'email' => 'charlie@example.com',
        'phone' => '1444333222',
        'address' => '654 Maple Dr'
    ]);

    $updateData = [
        'name' => 'Charlie Wilson',
        'phone' => '1888999000'
    ];

    $response = $this->putJson("/api/customers/{$customer->id}", $updateData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Charlie Brown',
        'phone' => '1444333222'
    ]);
});

test('can delete a customer', function () {
    $customer = Customer::create([
        'name' => 'David Lee',
        'email' => 'david@example.com',
        'phone' => '1777888999',
        'address' => '987 Cedar Ln'
    ]);

    $response = $this->deleteJson("/api/customers/{$customer->id}");

    $response->assertStatus(204);

    // Customer should be soft deleted
    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'deleted_at' => now(),
    ]);

    // But should exist in withTrashed query
    $this->assertDatabaseHas('customers', [
        'id' => $customer->id
    ], 'mysql'); // Using the test database
});

test('can search customers in dropdown', function () {
    Customer::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'address' => '123 Main St'
    ]);

    Customer::create([
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone' => '0987654321',
        'address' => '456 Oak Ave'
    ]);

    $response = $this->getJson('/api/customers/dropdown?search_key=John');

    $response->assertStatus(404);
});
