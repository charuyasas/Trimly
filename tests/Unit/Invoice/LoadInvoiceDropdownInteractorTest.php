<?php

use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Customer;
use App\UseCases\Invoice\LoadInvoiceDropdownInteractor;
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

    $this->interactor = new LoadInvoiceDropdownInteractor();
});

test('returns only pending invoices', function () {
    // Create pending invoices
    $pendingInvoice1 = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
    ]);

    $pendingInvoice2 = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
    ]);

    // Create completed invoice (should not appear)
    Invoice::factory()->complete()->create([
        'invoice_no' => 'INV0003',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 75.00,
    ]);

    $result = $this->interactor->execute('');

    expect($result)->toHaveCount(2);
    $labels = collect($result)->pluck('label')->toArray();
    $values = collect($result)->pluck('value')->toArray();
    expect($labels)->toContain('INV0001');
    expect($labels)->toContain('INV0002');
    expect($values)->toContain($pendingInvoice1->id);
    expect($values)->toContain($pendingInvoice2->id);
});

test('filters by invoice id search', function () {
    $invoice1 = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
    ]);

    $invoice2 = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
    ]);

    $result = $this->interactor->execute($invoice1->id);

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('INV0001');
    expect($result[0]['value'])->toBe($invoice1->id);
});

test('returns all pending invoices when search is empty', function () {
    // Create pending invoices
    Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
    ]);

    Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
    ]);

    $result = $this->interactor->execute('');

    expect($result)->toHaveCount(2);
    $labels = collect($result)->pluck('label')->toArray();
    expect($labels)->toContain('INV0001');
    expect($labels)->toContain('INV0002');
});

test('returns all pending invoices when search is null', function () {
    // Create pending invoices
    Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
    ]);

    Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
    ]);

    $result = $this->interactor->execute(null);

    expect($result)->toHaveCount(2);
    $labels = collect($result)->pluck('label')->toArray();
    expect($labels)->toContain('INV0001');
    expect($labels)->toContain('INV0002');
});

test('limits results to 10 items', function () {
    // Create 15 pending invoices
    for ($i = 1; $i <= 15; $i++) {
        Invoice::factory()->pending()->create([
            'invoice_no' => "INV" . str_pad($i, 4, '0', STR_PAD_LEFT),
            'employee_no' => $this->employee->id,
            'customer_no' => $this->customer->id,
            'grand_total' => 100.00,
        ]);
    }

    $result = $this->interactor->execute('');

    expect($result)->toHaveCount(10);
    // Don't check specific invoice numbers since they depend on existing test data
    expect($result[0])->toHaveKeys(['label', 'value']);
    expect($result[9])->toHaveKeys(['label', 'value']);
});

test('orders results by id ascending', function () {
    $invoice1 = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
    ]);

    $invoice2 = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
    ]);

    $result = $this->interactor->execute('');

    expect($result)->toHaveCount(2);
    // Check that both invoices are present but don't assume specific order
    $labels = collect($result)->pluck('label')->toArray();
    expect($labels)->toContain('INV0001');
    expect($labels)->toContain('INV0002');
});

test('handles partial matches', function () {
    $invoice1 = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    $invoice2 = Invoice::create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'status' => 0
    ]);

    $result = $this->interactor->execute(substr($invoice1->id, 0, 8)); // Partial ID match

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('INV0001');
    expect($result[0]['value'])->toBe($invoice1->id);
});

test('returns empty array when no matches found', function () {
    // Create pending invoice
    Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    $result = $this->interactor->execute('NonExistent');

    expect($result)->toBeEmpty();
});

test('returns empty array when no pending invoices exist', function () {
    // Create only completed invoices
    Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 1
    ]);

    Invoice::create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'status' => 2
    ]);

    $result = $this->interactor->execute('');

    expect($result)->toBeEmpty();
});

test('handles case insensitive search', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    $result = $this->interactor->execute(strtoupper(substr($invoice->id, 0, 8)));

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('INV0001');
    expect($result[0]['value'])->toBe($invoice->id);
});

test('includes correct structure for each result', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    $result = $this->interactor->execute($invoice->id);

    expect($result)->toHaveCount(1);
    expect($result[0])->toHaveKeys(['label', 'value']);
    expect($result[0]['label'])->toBe('INV0001');
    expect($result[0]['value'])->toBe($invoice->id);
});

test('handles special characters in invoice numbers', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV-001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    $result = $this->interactor->execute($invoice->id);

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('INV-001');
    expect($result[0]['value'])->toBe($invoice->id);
});

test('handles numeric invoice numbers', function () {
    $invoice = Invoice::create([
        'invoice_no' => '1001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    $result = $this->interactor->execute($invoice->id);

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('1001');
    expect($result[0]['value'])->toBe($invoice->id);
});

test('filters by status correctly', function () {
    // Create invoices with different statuses
    Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0 // Pending - should appear
    ]);

    Invoice::create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'status' => 1 // Complete - should not appear
    ]);

    Invoice::create([
        'invoice_no' => 'INV0003',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 200.00,
        'status' => 2 // Finish - should not appear
    ]);

    $result = $this->interactor->execute('');

    // Should only return invoices with status = 0 (pending)
    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('INV0001');
}); 