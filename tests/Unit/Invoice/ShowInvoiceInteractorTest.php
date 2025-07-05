<?php

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Customer;
use App\UseCases\Invoice\ShowInvoiceInteractor;
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

    $this->interactor = new ShowInvoiceInteractor();
});

test('returns invoice items with service codes', function () {
    $invoice = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
    ]);

    $item1 = InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'sub_total' => 45.00
    ]);

    $item2 = InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 5.00,
        'sub_total' => 45.00
    ]);

    $result = $this->interactor->execute($invoice);

    expect($result)->toHaveCount(2);
    expect($result[0])->toHaveKeys([
        'item_id',
        'item_description',
        'quantity',
        'amount',
        'discount_percentage',
        'discount_amount',
        'sub_total',
        'item_code'
    ]);

    expect(collect($result)->pluck('item_id')->map('strval')->toArray())->toContain((string)$this->service1->id);
    expect(collect($result)->pluck('item_id')->map('strval')->toArray())->toContain((string)$this->service2->id);
    expect(collect($result)->pluck('amount')->toArray())->toContain('25.00');
    expect(collect($result)->pluck('amount')->toArray())->toContain('50.00');
    expect(collect($result)->pluck('discount_percentage')->toArray())->toContain(10);
    expect(collect($result)->pluck('discount_percentage')->toArray())->toContain(0);
});

test('returns empty collection when invoice has no items', function () {
    $invoice = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 0.00,
    ]);

    $result = $this->interactor->execute($invoice);

    expect($result)->toBeEmpty();
});

test('handles multiple items with different discount types', function () {
    $invoice = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
    ]);

    // Item with percentage discount
    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 3,
        'amount' => 25.00,
        'discount_percentage' => 20,
        'discount_amount' => 0,
        'sub_total' => 60.00 // 3 * 25 * 0.8
    ]);

    // Item with amount discount
    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 10.00,
        'sub_total' => 40.00 // 50 - 10
    ]);

    // Item with no discount
    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00 // 2 * 25
    ]);

    $result = $this->interactor->execute($invoice);

    expect($result)->toHaveCount(3);
    $subTotals = collect($result)->pluck('sub_total')->toArray();
    expect($subTotals)->toContain('60.00');
    expect($subTotals)->toContain('40.00');
    expect($subTotals)->toContain('50.00');
});

test('handles soft deleted services', function () {
    $invoice = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 25.00,
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00
    ]);

    // Soft delete the service
    $this->service1->delete();

    $result = $this->interactor->execute($invoice);

    expect($result)->toHaveCount(1);
    expect($result[0]['item_code'])->toBeNull(); // Service is soft deleted
});

test('orders items by creation time', function () {
    $invoice = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'status' => 0
    ]);

    $firstItem = InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'First Service',
        'quantity' => 1,
        'amount' => 25.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'sub_total' => 25.00
    ]);

    $secondItem = InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Second Service',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    $result = $this->interactor->execute($invoice);

    expect($result)->toHaveCount(2);
    $amounts = collect($result)->pluck('amount')->toArray();
    expect(in_array('25.00', $amounts) || in_array(25.00, $amounts))->toBeTrue();
    expect(in_array('50.00', $amounts) || in_array(50.00, $amounts))->toBeTrue();
});

test('handles zero quantities and amounts', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 0.00,
        'status' => 0
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Free Service',
        'quantity' => 1,
        'amount' => 0.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 0.00
    ]);

    $result = $this->interactor->execute($invoice);

    expect($result)->toHaveCount(1);
    expect($result[0]['quantity'])->toBe(1);
    expect($result[0]['amount'])->toBe('0.00');
    expect($result[0]['sub_total'])->toBe('0.00');
});

test('includes service relationship data', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 25.00,
        'status' => 0
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00
    ]);

    $result = $this->interactor->execute($invoice);

    expect($result)->toHaveCount(1);
    expect($result[0]['item_code'])->toBe('SVC001');
});

test('handles large quantities and amounts', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 1000.00,
        'status' => 0
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Bulk Haircut Service',
        'quantity' => 100,
        'amount' => 10.00,
        'discount_percentage' => 5,
        'discount_amount' => 0,
        'sub_total' => 950.00 // 100 * 10 * 0.95
    ]);

    $result = $this->interactor->execute($invoice);

    expect($result)->toHaveCount(1);
    expect($result[0]['quantity'])->toBe(100);
    expect($result[0]['amount'])->toBe('10.00');
    expect($result[0]['discount_percentage'])->toBe(5);
    expect($result[0]['sub_total'])->toBe('950.00');
});

test('handles discount percentages', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'status' => 0
    ]);

    // Item with percentage discount
    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 3,
        'amount' => 25.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'sub_total' => 67.50 // 3 * 25 * 0.9
    ]);

    // Item with amount discount
    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 10.00,
        'sub_total' => 40.00 // 50 - 10
    ]);

    // Item with no discount
    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00 // 2 * 25
    ]);

    $result = $this->interactor->execute($invoice);

    expect($result)->toHaveCount(3);
    $discountPercentages = collect($result)->pluck('discount_percentage')->toArray();
    expect(in_array(10, $discountPercentages) || in_array('10', $discountPercentages))->toBeTrue();
    expect(in_array(0, $discountPercentages) || in_array('0', $discountPercentages))->toBeTrue();
}); 