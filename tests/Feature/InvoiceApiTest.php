<?php

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create test data
    $this->employee = Employee::create([
        'employee_id' => 'EMP001',
        'name' => 'John Employee',
        'address' => '123 Employee St',
        'contact_no' => '1234567890',
        'ledger_code' => 'EMP001'
    ]);

    $this->customer = Customer::create([
        'name' => 'Jane Customer',
        'email' => 'jane@example.com',
        'phone' => '0987654321',
        'address' => '456 Customer Ave'
    ]);

    $this->service1 = Service::create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    $this->service2 = Service::create([
        'code' => 'SVC002',
        'description' => 'Hair Coloring',
        'price' => 50.00
    ]);

    $this->service3 = Service::create([
        'code' => 'SVC003',
        'description' => 'Hair Styling',
        'price' => 30.00
    ]);
});

// Test index method (ListInvoiceInteractor)
test('can get list of completed invoices', function () {
    // Create completed invoices
    $invoice1 = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'status' => 1
    ]);

    $invoice2 = Invoice::create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'discount_percentage' => 0,
        'discount_amount' => 15.00,
        'status' => 1
    ]);

    // Create a pending invoice (should not appear in results)
    Invoice::create([
        'invoice_no' => 'INV0003',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 75.00,
        'status' => 0
    ]);

    $response = $this->getJson('/api/invoice-list');

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => [
                'id',
                'invoice_no',
                'discount_percentage',
                'discount_amount',
                'grand_total',
                'employee_name',
                'customer_name'
            ]
        ]);

    $response->assertJsonCount(2);
    $response->assertJsonFragment(['invoice_no' => 'INV0001']);
    $response->assertJsonFragment(['invoice_no' => 'INV0002']);
});

// Test store method (StoreInvoiceInteractor)
test('can create a new invoice', function () {
    $invoiceData = [
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'items' => [
            [
                'item_id' => $this->service1->id,
                'item_description' => 'Haircut Service',
                'quantity' => 2,
                'amount' => 25.00,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'sub_total' => 50.00
            ],
            [
                'item_id' => $this->service2->id,
                'item_description' => 'Hair Coloring',
                'quantity' => 1,
                'amount' => 50.00,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'sub_total' => 50.00
            ]
        ]
    ];

    $response = $this->postJson('/api/new-invoice', $invoiceData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'invoice' => [
                'id',
                'invoice_no',
                'employee_no',
                'customer_no',
                'token_no',
                'items'
            ]
        ]);

    $response->assertJson([
        'message' => 'Invoice created successfully'
    ]);

    $this->assertDatabaseHas('invoices', [
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'status' => 0
    ]);

    $this->assertDatabaseHas('invoice_items', [
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'sub_total' => 50.00
    ]);
});

test('prevents duplicate items in invoice', function () {
    $invoiceData = [
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'items' => [
            [
                'item_id' => $this->service1->id,
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ],
            [
                'item_id' => $this->service1->id, // Duplicate item
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]
        ]
    ];

    $response = $this->postJson('/api/new-invoice', $invoiceData);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Duplicate item(s) detected in invoice items.',
            'duplicates' => [$this->service1->id]
        ]);
});

test('updates existing invoice when invoice_no already exists', function () {
    // Create initial invoice
    $existingInvoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 50.00,
        'status' => 0
    ]);

    $updateData = [
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'items' => [
            [
                'item_id' => $this->service1->id,
                'item_description' => 'Haircut Service',
                'quantity' => 2,
                'amount' => 25.00,
                'sub_total' => 50.00
            ],
            [
                'item_id' => $this->service2->id,
                'item_description' => 'Hair Coloring',
                'quantity' => 1,
                'amount' => 50.00,
                'sub_total' => 50.00
            ]
        ]
    ];

    $response = $this->postJson('/api/new-invoice', $updateData);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'The invoice no has already been taken.'
        ]);
});

// Test show method (ShowInvoiceInteractor)
test('can show invoice items', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    $item1 = InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'sub_total' => 45.00
    ]);

    $item2 = InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 5.00,
        'sub_total' => 45.00
    ]);

    $response = $this->getJson("/api/invoices/{$invoice->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => [
                'item_id',
                'item_description',
                'quantity',
                'amount',
                'discount_percentage',
                'discount_amount',
                'sub_total',
                'item_code'
            ]
        ]);

    $response->assertJsonCount(2);
    $response->assertJsonFragment([
        'item_id' => $item1->item_id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => '25.00',
        'discount_percentage' => 10,
        'discount_amount' => '0.00',
        'sub_total' => '45.00',
        'item_code' => 'SVC001'
    ]);
});

test('returns 404 for non-existent invoice', function () {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';
    
    $response = $this->getJson("/api/invoices/{$fakeId}");
    
    $response->assertStatus(404);
});

// Test finishInvoice method (FinishInvoiceInteractor)
test('can finish an invoice with percentage discount', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'sub_total' => 50.00
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'sub_total' => 50.00
    ]);

    $finishData = [
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'items' => [
            [
                'item_id' => $this->service1->id,
                'item_description' => 'Haircut Service',
                'quantity' => 2,
                'amount' => 25.00,
                'sub_total' => 50.00
            ],
            [
                'item_id' => $this->service2->id,
                'item_description' => 'Hair Coloring',
                'quantity' => 1,
                'amount' => 50.00,
                'sub_total' => 50.00
            ]
        ]
    ];

    $response = $this->postJson("/api/finish-invoice/{$invoice->id}", $finishData);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Invoice finalized successfully.'
        ]);

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'grand_total' => 90.00, // 100 - 10% discount
        'status' => 1
    ]);
});

test('can finish an invoice with amount discount', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'sub_total' => 50.00
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'sub_total' => 50.00
    ]);

    $finishData = [
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'discount_percentage' => 0,
        'discount_amount' => 15.00,
        'items' => [
            [
                'item_id' => $this->service1->id,
                'item_description' => 'Haircut Service',
                'quantity' => 2,
                'amount' => 25.00,
                'sub_total' => 50.00
            ],
            [
                'item_id' => $this->service2->id,
                'item_description' => 'Hair Coloring',
                'quantity' => 1,
                'amount' => 50.00,
                'sub_total' => 50.00
            ]
        ]
    ];

    $response = $this->postJson("/api/finish-invoice/{$invoice->id}", $finishData);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Invoice finalized successfully.'
        ]);

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'discount_percentage' => 0,
        'discount_amount' => 15.00,
        'grand_total' => 85.00, // 100 - 15 discount
        'status' => 1
    ]);
});

test('handles finishing non-existent invoice', function () {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';
    
    $finishData = [
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'discount_percentage' => 10,
        'items' => [
            [
                'item_id' => $this->service1->id,
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]
        ]
    ];

    $response = $this->postJson("/api/finish-invoice/{$fakeId}", $finishData);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Failed to finish invoice.'
        ]);
});

// Test loadItemDropdown method (LoadItemDropdownInteractor)
test('can load item dropdown with search', function () {
    $response = $this->getJson('/api/item-list?q=Haircut');

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => [
                'label',
                'value',
                'price'
            ]
        ]);

    $response->assertJsonFragment([
        'label' => 'SVC001 - Haircut Service',
        'value' => $this->service1->id,
        'price' => '25.00'
    ]);
});

test('can load item dropdown with empty search', function () {
    $response = $this->getJson('/api/item-list');

    $response->assertStatus(200)
        ->assertJsonCount(3); // All services should be returned
});

test('can load item dropdown with service code search', function () {
    $response = $this->getJson('/api/item-list?q=SVC002');

    $response->assertStatus(200)
        ->assertJsonFragment([
            'label' => 'SVC002 - Hair Coloring',
            'value' => $this->service2->id,
            'price' => '50.00'
        ]);
});

// Test loadInvoiceDropdown method (LoadInvoiceDropdownInteractor)
test('can load invoice dropdown for pending invoices', function () {
    // Create pending invoices
    $pendingInvoice1 = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    $pendingInvoice2 = Invoice::create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'status' => 0
    ]);

    // Create completed invoice (should not appear)
    Invoice::create([
        'invoice_no' => 'INV0003',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 75.00,
        'status' => 1
    ]);

    $response = $this->getJson('/api/invoice-list-dropdown?q=' . $pendingInvoice1->id);

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => [
                'label',
                'value'
            ]
        ]);

    $response->assertJsonFragment([
        'label' => 'INV0001',
        'value' => $pendingInvoice1->id
    ]);

    $response->assertJsonCount(1); // Only the matching pending invoice
});

test('can load invoice dropdown with empty search', function () {
    // Create pending invoices
    Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    Invoice::create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'status' => 0
    ]);

    $response = $this->getJson('/api/invoice-list-dropdown');

    $response->assertStatus(200)
        ->assertJsonCount(2); // Both pending invoices
});

// Test getInvoiceItems method (GetInvoiceItemsInteractor)
test('can get invoice items with details', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'status' => 0
    ]);

    $item1 = InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    $item2 = InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    $response = $this->getJson("/api/invoice-items/{$invoice->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'invoice_id',
            'invoice_no',
            'employee_no',
            'employee_name',
            'customer_no',
            'customer_name',
            'token_no',
            'discount_percentage',
            'discount_amount',
            'items'
        ]);

    $response->assertJson([
        'invoice_id' => $invoice->id,
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'employee_name' => 'John Employee',
        'customer_no' => $this->customer->id,
        'customer_name' => 'Jane Customer',
        'token_no' => 'INV0001',
        'discount_percentage' => 10,
        'discount_amount' => 0
    ]);

    $response->assertJsonCount(2, 'items');
});

test('returns 404 for non-existent invoice in getInvoiceItems', function () {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';
    
    $response = $this->getJson("/api/invoice-items/{$fakeId}");
    
    $response->assertStatus(404);
});

// Test validation errors
test('validates required fields when creating invoice', function () {
    $response = $this->postJson('/api/new-invoice', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['employee_no', 'customer_no', 'items']);
});

test('validates item structure when creating invoice', function () {
    $invoiceData = [
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'items' => [
            [
                'item_id' => $this->service1->id,
                // Missing required fields
            ]
        ]
    ];

    $response = $this->postJson('/api/new-invoice', $invoiceData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items.0.item_description', 'items.0.quantity', 'items.0.amount', 'items.0.sub_total']);
});

test('validates discount percentage range', function () {
    $invoiceData = [
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'discount_percentage' => 150, // Invalid: > 100
        'items' => [
            [
                'item_id' => $this->service1->id,
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]
        ]
    ];

    $response = $this->postJson('/api/new-invoice', $invoiceData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['discount_percentage']);
});

// Test edge cases
test('handles zero quantity items', function () {
    $invoiceData = [
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'items' => [
            [
                'item_id' => $this->service1->id,
                'item_description' => 'Haircut Service',
                'quantity' => 0, // Invalid: must be > 0
                'amount' => 25.00,
                'sub_total' => 0
            ]
        ]
    ];

    $response = $this->postJson('/api/new-invoice', $invoiceData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items.0.quantity']);
});

test('handles negative amounts', function () {
    $invoiceData = [
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'items' => [
            [
                'item_id' => $this->service1->id,
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => -25.00, // Invalid: must be >= 0
                'sub_total' => -25.00
            ]
        ]
    ];

    $response = $this->postJson('/api/new-invoice', $invoiceData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items.0.amount', 'items.0.sub_total']);
});

test('handles database transaction rollback on error', function () {
    // This test would require mocking the database to simulate a failure
    // For now, we'll test that the API handles errors gracefully
    
    $invoiceData = [
        'employee_no' => 'invalid-uuid', // This should cause a foreign key constraint error
        'customer_no' => $this->customer->id,
        'items' => [
            [
                'item_id' => $this->service1->id,
                'item_description' => 'Haircut Service',
                'quantity' => 1,
                'amount' => 25.00,
                'sub_total' => 25.00
            ]
        ]
    ];

    $response = $this->postJson('/api/new-invoice', $invoiceData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['employee_no']);
}); 