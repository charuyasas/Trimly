<?php

use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Customer;
use App\UseCases\Invoice\ListInvoiceInteractor;
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

    $this->interactor = new ListInvoiceInteractor();
});

test('returns only completed invoices', function () {
    // Create completed invoices
    $invoice1 = Invoice::factory()->complete()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
    ]);

    $invoice2 = Invoice::factory()->complete()->create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'discount_percentage' => 0,
        'discount_amount' => 15.00,
    ]);

    // Create a pending invoice (should not appear in results)
    Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0003',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 75.00,
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(2);
    // Check for presence, not order
    $invoiceNos = collect($result)->pluck('invoice_no')->toArray();
    $grandTotals = collect($result)->pluck('grand_total')->toArray();
    $discountPercentages = collect($result)->pluck('discount_percentage')->toArray();
    
    expect($invoiceNos)->toContain('INV0001');
    expect($invoiceNos)->toContain('INV0002');
    expect($grandTotals)->toContain('100.00');
    expect($grandTotals)->toContain('150.00');
    expect($discountPercentages)->toContain(10);
    expect($discountPercentages)->toContain(0);
});

test('orders invoices by created_at descending', function () {
    // Create invoices with different creation times
    $olderInvoice = Invoice::factory()->complete()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'created_at' => now()->subDays(2)
    ]);

    $newerInvoice = Invoice::factory()->complete()->create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'created_at' => now()->subDay(1)
    ]);

    $latestInvoice = Invoice::factory()->complete()->create([
        'invoice_no' => 'INV0003',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 200.00,
        'created_at' => now()
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(3);
    expect($result[0]['invoice_no'])->toBe('INV0003'); // Latest first
    expect($result[1]['invoice_no'])->toBe('INV0002');
    expect($result[2]['invoice_no'])->toBe('INV0001'); // Oldest last
});

test('includes employee and customer names in results', function () {
    $invoice = Invoice::factory()->complete()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(1);
    expect($result[0]['employee_name'])->toBe('John Employee');
    expect($result[0]['customer_name'])->toBe('Jane Customer');
});

test('returns empty array when no completed invoices exist', function () {
    // Create only pending invoices
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

    $result = $this->interactor->execute();

    expect($result)->toBeEmpty();
});

test('handles multiple employees and customers', function () {
    $employee2 = Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Jane Employee',
        'address' => '456 Employee St',
        'contact_no' => '0987654321',
        'ledger_code' => 'EMP002'
    ]);

    $customer2 = Customer::factory()->create([
        'name' => 'Bob Customer',
        'email' => 'bob@example.com',
        'phone' => '1122334455',
        'address' => '789 Customer Ave'
    ]);

    // Create invoices with different employees and customers
    $invoice1 = Invoice::factory()->complete()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
    ]);

    $invoice2 = Invoice::factory()->complete()->create([
        'invoice_no' => 'INV0002',
        'employee_no' => $employee2->id,
        'customer_no' => $customer2->id,
        'grand_total' => 150.00,
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(2);
    $employeeNames = collect($result)->pluck('employee_name')->toArray();
    $customerNames = collect($result)->pluck('customer_name')->toArray();
    expect($employeeNames)->toContain('Jane Employee');
    expect($employeeNames)->toContain('John Employee');
    expect($customerNames)->toContain('Jane Customer');
    expect($customerNames)->toContain('Bob Customer');
});

test('includes all required fields in result', function () {
    $invoice = Invoice::factory()->complete()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'discount_percentage' => 10,
        'discount_amount' => 5.00,
        'status' => 1
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(1);
    expect($result[0])->toHaveKeys([
        'id',
        'invoice_no',
        'discount_percentage',
        'discount_amount',
        'grand_total',
        'employee_name',
        'customer_name'
    ]);

    expect($result[0]['id'])->toBe($invoice->id);
    expect($result[0]['invoice_no'])->toBe('INV0001');
    expect($result[0]['discount_percentage'])->toBe(10);
    expect($result[0]['discount_amount'])->toBe('5.00');
    expect($result[0]['grand_total'])->toBe('100.00');
});

test('handles soft deleted employees and customers', function () {
    // Soft delete the employee
    $this->employee->delete();

    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 1
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(1);
    // Should still show the invoice even if employee is soft deleted
    expect($result[0]['invoice_no'])->toBe('INV0001');
});

test('filters by status correctly', function () {
    // Create invoices with different statuses
    Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0 // Pending
    ]);

    Invoice::create([
        'invoice_no' => 'INV0002',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'status' => 1 // Complete
    ]);

    Invoice::create([
        'invoice_no' => 'INV0003',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 200.00,
        'status' => 2 // Finish
    ]);

    $result = $this->interactor->execute();

    // Should only return invoices with status = 1 (complete)
    expect($result)->toHaveCount(1);
    expect($result[0]['invoice_no'])->toBe('INV0002');
}); 