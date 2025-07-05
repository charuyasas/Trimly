<?php

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Customer;
use App\UseCases\Invoice\StoreInvoiceInteractor;
use App\UseCases\Invoice\Requests\InvoiceRequest;
use App\UseCases\Invoice\Requests\InvoiceItemRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'John Employee',
        'address' => '123 Employee St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $this->customer = Customer::factory()->create([
        'name' => 'Jane Customer',
        'email' => 'jane@example.com',
        'phone' => '0987654321',
        'address' => '456 Customer Ave'
    ]);

    $this->service1 = Service::factory()->create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    $this->service2 = Service::factory()->create([
        'code' => 'SVC002',
        'description' => 'Hair Coloring',
        'price' => 50.00
    ]);

    $this->interactor = new StoreInvoiceInteractor();
});

test('creates new invoice successfully', function () {
    $items = [
        InvoiceItemRequest::from([
            'item_id' => $this->service1->id,
            'item_description' => 'Haircut Service',
            'quantity' => 2,
            'amount' => 25.00,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'sub_total' => 50.00
        ]),
        InvoiceItemRequest::from([
            'item_id' => $this->service2->id,
            'item_description' => 'Hair Coloring',
            'quantity' => 1,
            'amount' => 50.00,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'sub_total' => 50.00
        ])
    ];

    $request = InvoiceRequest::from([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'status' => false,
        'items' => $items
    ]);

    $result = $this->interactor->execute($request);

    expect($result['status'])->toBe(201);
    expect($result['response']['message'])->toBe('Invoice created successfully');
    expect($result['response']['invoice']['invoice_no'])->toBe('INV0001');
    expect((string)$result['response']['invoice']['employee_no'])->toBe((string)$this->employee->id);
    expect((string)$result['response']['invoice']['customer_no'])->toBe((string)$this->customer->id);

    $this->assertDatabaseHas('invoices', [
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'status' => 0
    ]);

    $this->assertDatabaseCount('invoice_items', 2);
});

test('prevents duplicate items in invoice', function () {
    $items = [
        InvoiceItemRequest::from([
            'item_id' => $this->service1->id,
            'item_description' => 'Haircut Service',
            'quantity' => 1,
            'amount' => 25.00,
            'sub_total' => 25.00
        ]),
        InvoiceItemRequest::from([
            'item_id' => $this->service1->id, // Duplicate item
            'item_description' => 'Haircut Service',
            'quantity' => 1,
            'amount' => 25.00,
            'sub_total' => 25.00
        ])
    ];

    $request = InvoiceRequest::from([
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 50.00,
        'items' => $items
    ]);

    $result = $this->interactor->execute($request);

    expect($result['status'])->toBe(422);
    expect($result['response']['message'])->toBe('Duplicate item(s) detected in invoice items.');
    expect(collect($result['response']['duplicates'])->map('strval')->toArray())->toContain((string)$this->service1->id);

    $this->assertDatabaseCount('invoices', 0);
    $this->assertDatabaseCount('invoice_items', 0);
});

test('updates existing invoice when invoice_no exists', function () {
    // Create existing invoice
    $existingInvoice = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 50.00,
    ]);

    $items = [
        InvoiceItemRequest::from([
            'item_id' => $this->service1->id,
            'item_description' => 'Haircut Service',
            'quantity' => 2,
            'amount' => 25.00,
            'sub_total' => 50.00
        ])
    ];

    $request = InvoiceRequest::from([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 50.00,
        'items' => $items
    ]);

    $result = $this->interactor->execute($request);

    expect($result['status'])->toBe(200);
    expect($result['response']['message'])->toBe('Invoice updated.');

    // Should update the existing invoice, not delete or create a new one
    $this->assertDatabaseHas('invoices', [
        'id' => $existingInvoice->id,
        'invoice_no' => 'INV0001',
        'grand_total' => 50.00
    ]);
});

test('recalculates grand total from items', function () {
    $items = [
        InvoiceItemRequest::from([
            'item_id' => $this->service1->id,
            'item_description' => 'Haircut Service',
            'quantity' => 2,
            'amount' => 25.00,
            'discount_amount' => 5.00,
            'sub_total' => 45.00
        ]),
        InvoiceItemRequest::from([
            'item_id' => $this->service2->id,
            'item_description' => 'Hair Coloring',
            'quantity' => 1,
            'amount' => 50.00,
            'discount_amount' => 10.00,
            'sub_total' => 40.00
        ])
    ];

    $request = InvoiceRequest::from([
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 200.00, // This should be recalculated
        'items' => $items
    ]);

    $result = $this->interactor->execute($request);

    expect($result['status'])->toBe(201);

    // Grand total should be recalculated: (2*25-5) + (1*50-10) = 45 + 40 = 85
    $this->assertDatabaseHas('invoices', [
        'grand_total' => 85.00
    ]);
});

test('generates next invoice number correctly', function () {
    // Create some existing invoices
    Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 50.00,
        'status' => 0
    ]);

    Invoice::create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 75.00,
        'status' => 0
    ]);

    $items = [
        InvoiceItemRequest::from([
            'item_id' => $this->service1->id,
            'item_description' => 'Haircut Service',
            'quantity' => 1,
            'amount' => 25.00,
            'sub_total' => 25.00
        ])
    ];

    $request = InvoiceRequest::from([
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 25.00,
        'items' => $items
    ]);

    $result = $this->interactor->execute($request);

    expect($result['status'])->toBe(201);

    // Should generate INV0003
    $this->assertDatabaseHas('invoices', [
        'invoice_no' => 'INV0003'
    ]);
});

test('generates first invoice number when no invoices exist', function () {
    $items = [
        InvoiceItemRequest::from([
            'item_id' => $this->service1->id,
            'item_description' => 'Haircut Service',
            'quantity' => 1,
            'amount' => 25.00,
            'sub_total' => 25.00
        ])
    ];

    $request = InvoiceRequest::from([
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 25.00,
        'items' => $items
    ]);

    $result = $this->interactor->execute($request);

    expect($result['status'])->toBe(201);

    // Should generate INV0001
    $this->assertDatabaseHas('invoices', [
        'invoice_no' => 'INV0001'
    ]);
});

test('handles database transaction rollback on error', function () {
    $items = [
        InvoiceItemRequest::from([
            'item_id' => $this->service1->id,
            'item_description' => 'Haircut Service',
            'quantity' => 1,
            'amount' => 25.00,
            'sub_total' => 25.00
        ])
    ];

    $request = InvoiceRequest::from([
        'employee_no' => 'invalid-uuid', // This will cause a foreign key constraint error
        'customer_no' => $this->customer->id,
        'grand_total' => 25.00,
        'items' => $items
    ]);

    $result = $this->interactor->execute($request);

    expect($result['status'])->toBe(500);
    expect($result['response']['message'])->toBe('Failed to process invoice');
    expect($result['response'])->toHaveKey('error');
});

test('creates invoice items with correct calculations', function () {
    $items = [
        InvoiceItemRequest::from([
            'item_id' => $this->service1->id,
            'item_description' => 'Haircut Service',
            'quantity' => 3,
            'amount' => 25.00,
            'discount_percentage' => 10,
            'discount_amount' => 0,
            'sub_total' => 75.00 // (3 * 25) - 0
        ]),
        InvoiceItemRequest::from([
            'item_id' => $this->service2->id,
            'item_description' => 'Hair Coloring',
            'quantity' => 1,
            'amount' => 50.00,
            'discount_percentage' => 0,
            'discount_amount' => 5.00,
            'sub_total' => 45.00 // (1 * 50) - 5
        ])
    ];

    $request = InvoiceRequest::from([
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 120.00,
        'items' => $items
    ]);

    $result = $this->interactor->execute($request);

    expect($result['status'])->toBe(201);

    $this->assertDatabaseHas('invoice_items', [
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 3,
        'amount' => 25.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'sub_total' => 75.00
    ]);

    $this->assertDatabaseHas('invoice_items', [
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 5.00,
        'sub_total' => 45.00
    ]);
});
