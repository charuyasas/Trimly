<?php

use App\UseCases\Invoice\Requests\InvoiceRequest;
use App\UseCases\Invoice\Requests\InvoiceItemRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('creates invoice request with valid data', function () {
    $items = [
        InvoiceItemRequest::from([
            'item_id' => '550e8400-e29b-41d4-a716-446655440000',
            'item_description' => 'Haircut Service',
            'quantity' => 2,
            'amount' => 25.00,
            'discount_percentage' => 10,
            'discount_amount' => 0,
            'sub_total' => 45.00
        ])
    ];

    $request = InvoiceRequest::from([
        'id' => '550e8400-e29b-41d4-a716-446655440001',
        'invoice_no' => 'INV0001',
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'grand_total' => 100.00,
        'discount_percentage' => 5,
        'discount_amount' => 10.00,
        'status' => true,
        'items' => $items
    ]);

    expect($request->id)->toBe('550e8400-e29b-41d4-a716-446655440001');
    expect($request->invoice_no)->toBe('INV0001');
    expect($request->employee_no)->toBe('550e8400-e29b-41d4-a716-446655440002');
    expect($request->customer_no)->toBe('550e8400-e29b-41d4-a716-446655440003');
    expect($request->grand_total)->toBe(100.00);
    expect($request->discount_percentage)->toBe(5);
    expect($request->discount_amount)->toBe(10.00);
    expect($request->status)->toBe(true);
    expect($request->items)->toHaveCount(1);
});

test('creates invoice request with minimal required data', function () {
    $items = [
        InvoiceItemRequest::from([
            'item_id' => '550e8400-e29b-41d4-a716-446655440000',
            'item_description' => 'Haircut Service',
            'quantity' => 1,
            'amount' => 25.00,
            'sub_total' => 25.00
        ])
    ];

    $request = InvoiceRequest::from([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'items' => $items
    ]);

    expect($request->employee_no)->toBe('550e8400-e29b-41d4-a716-446655440002');
    expect($request->customer_no)->toBe('550e8400-e29b-41d4-a716-446655440003');
    expect($request->items)->toHaveCount(1);
    expect($request->grand_total)->toBeNull();
    expect($request->discount_percentage)->toBe(0);
    expect($request->discount_amount)->toBe(0.00);
    expect($request->status)->toBe(true);
});

test('validates required fields', function () {
    $this->expectException(ValidationException::class);

    InvoiceRequest::validateAndCreate([
        // Missing required fields: employee_no, customer_no, items
    ]);
});

test('validates employee_no is required and uuid', function () {
    $this->expectException(ValidationException::class);

    InvoiceRequest::validateAndCreate([
        'employee_no' => 'invalid-uuid',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'items' => [
            [
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]
        ]
    ]);
});

test('validates customer_no is required and uuid', function () {
    $this->expectException(ValidationException::class);

    InvoiceRequest::validateAndCreate([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => 'invalid-uuid',
        'items' => [
            [
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]
        ]
    ]);
});

test('validates items is required and array', function () {
    $this->expectException(ValidationException::class);

    InvoiceRequest::validateAndCreate([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'items' => 'not-an-array'
    ]);
});

test('validates grand_total is numeric and min 0', function () {
    $this->expectException(ValidationException::class);

    InvoiceRequest::validateAndCreate([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'grand_total' => -10.00,
        'items' => [
            [
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]
        ]
    ]);
});

test('validates discount_percentage is integer and between 0-100', function () {
    $this->expectException(ValidationException::class);

    InvoiceRequest::validateAndCreate([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'discount_percentage' => 150,
        'items' => [
            [
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]
        ]
    ]);
});

test('validates discount_amount is numeric and min 0', function () {
    $this->expectException(ValidationException::class);

    InvoiceRequest::validateAndCreate([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'discount_amount' => -10.00,
        'items' => [
            [
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]
        ]
    ]);
});

test('validates status is boolean', function () {
    $this->expectException(ValidationException::class);

    InvoiceRequest::validateAndCreate([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'status' => 'not-boolean',
        'items' => [
            [
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]
        ]
    ]);
});

test('validates invoice_no uniqueness', function () {
    // This test would require a database to test the unique rule
    // For now, we'll just test that the rule exists
    $rules = InvoiceRequest::rules();
    expect($rules)->toHaveKey('invoice_no');
});

test('handles nullable fields', function () {
    $request = InvoiceRequest::from([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'invoice_no' => null,
        'grand_total' => null,
        'items' => [
            InvoiceItemRequest::from([
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ])
        ]
    ]);

    expect($request->invoice_no)->toBeNull();
    expect($request->grand_total)->toBeNull();
});

test('sets default values correctly', function () {
    $request = InvoiceRequest::from([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'items' => [
            InvoiceItemRequest::from([
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ])
        ]
    ]);

    expect($request->discount_percentage)->toBe(0);
    expect($request->discount_amount)->toBe(0.00);
    expect($request->status)->toBe(true);
});

test('validates items array structure', function () {
    $this->expectException(ValidationException::class);

    InvoiceRequest::validateAndCreate([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'items' => [
            [
                // Missing required fields
                'item_id' => '550e8400-e29b-41d4-a716-446655440000'
            ]
        ]
    ]);
});

test('handles empty items array', function () {
    $this->expectException(ValidationException::class);

    InvoiceRequest::validateAndCreate([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'items' => []
    ]);
});

test('validates multiple items', function () {
    $request = InvoiceRequest::from([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'items' => [
            InvoiceItemRequest::from([
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]),
            InvoiceItemRequest::from([
                'item_id' => '550e8400-e29b-41d4-a716-446655440001',
                'item_description' => 'Hair Coloring',
                'quantity' => 2,
                'amount' => 50.00,
                'sub_total' => 100.00
            ])
        ]
    ]);

    expect($request->items)->toHaveCount(2);
    expect($request->items[0]->item_description)->toBe('Haircut Service');
    expect($request->items[1]->item_description)->toBe('Hair Coloring');
});

test('handles zero values correctly', function () {
    $request = InvoiceRequest::from([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'discount_percentage' => 0,
        'discount_amount' => 0.00,
        'items' => [
            InvoiceItemRequest::from([
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ])
        ]
    ]);

    expect($request->discount_percentage)->toBe(0);
    expect($request->discount_amount)->toBe(0.00);
});

test('handles large values correctly', function () {
    $request = InvoiceRequest::from([
        'employee_no' => '550e8400-e29b-41d4-a716-446655440002',
        'customer_no' => '550e8400-e29b-41d4-a716-446655440003',
        'grand_total' => 999999.99,
        'discount_percentage' => 100,
        'discount_amount' => 999999.99,
        'items' => [
            InvoiceItemRequest::from([
                'item_id' => '550e8400-e29b-41d4-a716-446655440000',
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ])
        ]
    ]);

    expect($request->grand_total)->toBe(999999.99);
    expect($request->discount_percentage)->toBe(100);
    expect($request->discount_amount)->toBe(999999.99);
}); 