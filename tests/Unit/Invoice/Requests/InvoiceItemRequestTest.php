<?php

use App\UseCases\Invoice\Requests\InvoiceItemRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('creates invoice item request with valid data', function () {
    $request = InvoiceItemRequest::from([
        'id' => '550e8400-e29b-41d4-a716-446655440000',
        'invoice_id' => '550e8400-e29b-41d4-a716-446655440001',
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 10,
        'discount_amount' => 5.00,
        'sub_total' => 45.00
    ]);

    expect($request->id)->toBe('550e8400-e29b-41d4-a716-446655440000');
    expect($request->invoice_id)->toBe('550e8400-e29b-41d4-a716-446655440001');
    expect($request->item_id)->toBe('550e8400-e29b-41d4-a716-446655440002');
    expect($request->item_description)->toBe('Haircut Service');
    expect($request->quantity)->toBe(2);
    expect($request->amount)->toBe(25.00);
    expect($request->discount_percentage)->toBe(10);
    expect($request->discount_amount)->toBe(5.00);
    expect($request->sub_total)->toBe(45.00);
});

test('creates invoice item request with minimal required data', function () {
    $request = InvoiceItemRequest::from([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00
    ]);

    expect($request->item_id)->toBe('550e8400-e29b-41d4-a716-446655440002');
    expect($request->item_description)->toBe('Haircut Service');
    expect($request->quantity)->toBe(1);
    expect($request->amount)->toBe(25.00);
    expect($request->sub_total)->toBe(25.00);
    expect($request->discount_percentage)->toBe(0);
    expect($request->discount_amount)->toBe(0.00);
});

test('validates required fields', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        // Missing required fields: item_id, item_description, quantity, amount, sub_total
    ]);
});

test('validates item_id is required', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00
    ]);
});

test('validates item_description is required', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00
    ]);
});

test('validates quantity is integer', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 'not-an-integer',
        'amount' => 25.00,
        'sub_total' => 25.00
    ]);
});

test('validates amount is numeric', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 'not-a-number',
        'sub_total' => 25.00
    ]);
});

test('validates sub_total is numeric', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 'not-a-number'
    ]);
});

test('validates discount_percentage is integer', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00,
        'discount_percentage' => 'not-an-integer'
    ]);
});

test('validates discount_amount is numeric', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00,
        'discount_amount' => 'not-a-number'
    ]);
});

test('handles nullable fields', function () {
    $request = InvoiceItemRequest::from([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00,
        'id' => null,
        'invoice_id' => null,
        'discount_percentage' => null,
        'discount_amount' => null
    ]);

    expect($request->id)->toBeNull();
    expect($request->invoice_id)->toBeNull();
    expect($request->discount_percentage)->toBeNull();
    expect($request->discount_amount)->toBeNull();
});

test('sets default values correctly', function () {
    $request = InvoiceItemRequest::from([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00
    ]);

    expect($request->discount_percentage)->toBe(0);
    expect($request->discount_amount)->toBe(0.00);
});

test('handles zero values', function () {
    $request = InvoiceItemRequest::from([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 0.00,
        'sub_total' => 0.00,
        'discount_percentage' => 0,
        'discount_amount' => 0.00
    ]);

    expect($request->amount)->toBe(0.00);
    expect($request->sub_total)->toBe(0.00);
    expect($request->discount_percentage)->toBe(0);
    expect($request->discount_amount)->toBe(0.00);
});

test('handles large values', function () {
    $request = InvoiceItemRequest::from([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Premium Service',
        'quantity' => 100,
        'amount' => 999.99,
        'sub_total' => 99999.00,
        'discount_percentage' => 100,
        'discount_amount' => 99999.00
    ]);

    expect($request->quantity)->toBe(100);
    expect($request->amount)->toBe(999.99);
    expect($request->sub_total)->toBe(99999.00);
    expect($request->discount_percentage)->toBe(100);
    expect($request->discount_amount)->toBe(99999.00);
});

test('validates quantity minimum value', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 0, // Must be at least 1
        'amount' => 25.00,
        'sub_total' => 0.00
    ]);
});

test('validates amount minimum value', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => -10.00, // Must be at least 0
        'sub_total' => -10.00
    ]);
});

test('validates sub_total minimum value', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => -10.00 // Must be at least 0
    ]);
});

test('validates discount_percentage range', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00,
        'discount_percentage' => 150 // Must be between 0-100
    ]);
});

test('validates discount_amount minimum value', function () {
    $this->expectException(ValidationException::class);

    InvoiceItemRequest::validateAndCreate([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00,
        'discount_amount' => -10.00 // Must be at least 0
    ]);
});

test('handles special characters in description', function () {
    $request = InvoiceItemRequest::from([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut & Styling (Premium)',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00
    ]);

    expect($request->item_description)->toBe('Haircut & Styling (Premium)');
});

test('handles edge cases', function () {
    $request = InvoiceItemRequest::from([
        'item_id' => '550e8400-e29b-41d4-a716-446655440002',
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 0.01,
        'sub_total' => 0.01,
        'discount_percentage' => 1,
        'discount_amount' => 0.01
    ]);

    expect($request->amount)->toBe(0.01);
    expect($request->sub_total)->toBe(0.01);
    expect($request->discount_percentage)->toBe(1);
    expect($request->discount_amount)->toBe(0.01);
}); 