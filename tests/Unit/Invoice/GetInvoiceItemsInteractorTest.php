<?php

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Customer;
use App\UseCases\Invoice\GetInvoiceItemsInteractor;
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

    $this->interactor = new GetInvoiceItemsInteractor();
});

test('returns invoice details with items', function () {
    $invoice = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
    ]);

    $item1 = InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    $item2 = InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    $result = $this->interactor->execute($invoice->id);

    expect($result)->toHaveKeys([
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

    expect($result['invoice_id'])->toBe($invoice->id);
    expect($result['invoice_no'])->toBe('INV0001');
    expect((string)$result['employee_no'])->toBe((string)$this->employee->id);
    expect($result['employee_name'])->toBe('John Employee');
    expect((string)$result['customer_no'])->toBe((string)$this->customer->id);
    expect($result['customer_name'])->toBe('Jane Customer');
    expect($result['token_no'])->toBe('INV0001');
    expect($result['discount_percentage'])->toBe(10);
    expect($result['discount_amount'])->toBe('0.00');

    expect($result['items'])->toHaveCount(2);
    $itemIds = collect($result['items'])->pluck('item_id')->map('strval')->toArray();
    expect($itemIds)->toContain((string)$item1->item_id);
    expect($itemIds)->toContain((string)$item2->item_id);
    $amounts = collect($result['items'])->pluck('amount')->toArray();
    expect($amounts)->toContain('25.00');
    expect($amounts)->toContain('50.00');
    $discountPercentages = collect($result['items'])->pluck('discount_percentage')->toArray();
    expect(in_array(10, $discountPercentages) || in_array('10', $discountPercentages))->toBeTrue();
    expect(in_array(0, $discountPercentages) || in_array('0', $discountPercentages))->toBeTrue();
});

test('returns empty items array when invoice has no items', function () {
    $invoice = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 0.00,
    ]);

    $result = $this->interactor->execute($invoice->id);

    expect($result['items'])->toBeEmpty();
    expect($result['invoice_id'])->toBe($invoice->id);
    expect($result['invoice_no'])->toBe('INV0001');
    expect($result['employee_name'])->toBe('John Employee');
    expect($result['customer_name'])->toBe('Jane Customer');
});

test('handles soft deleted employee and customer', function () {
    $invoice = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 25.00,
        'sub_total' => 25.00
    ]);

    // Soft delete the employee and customer
    $this->employee->delete();
    $this->customer->delete();

    $result = $this->interactor->execute($invoice->id);

    expect($result['employee_name'])->toBe(''); // Should be empty string
    expect($result['customer_name'])->toBe(''); // Should be empty string
    expect($result['items'])->toHaveCount(1);
});

test('hides created_at and updated_at from items', function () {
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

    $result = $this->interactor->execute($invoice->id);

    expect($result['items'])->toHaveCount(1);
    expect($result['items'][0])->not->toHaveKey('created_at');
    expect($result['items'][0])->not->toHaveKey('updated_at');
});

test('handles multiple items with different discount types', function () {
    $invoice = Invoice::factory()->pending()->create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 150.00,
        'discount_percentage' => 5,
        'discount_amount' => 10,
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
        'sub_total' => 60.00
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
        'sub_total' => 40.00
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
        'sub_total' => 50.00
    ]);

    $result = $this->interactor->execute($invoice->id);

    expect($result['discount_percentage'])->toBe(5);
    expect($result['discount_amount'])->toBe('10.00');
    expect($result['items'])->toHaveCount(3);

    $discountPercentages = collect($result['items'])->pluck('discount_percentage')->toArray();
    expect(in_array(20, $discountPercentages) || in_array('20', $discountPercentages))->toBeTrue();
    expect(in_array(0, $discountPercentages) || in_array('0', $discountPercentages))->toBeTrue();

    foreach ($result['items'] as $item) {
        expect(is_numeric($item['discount_percentage']));
    }
});

test('handles zero values correctly', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 0.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
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

    $result = $this->interactor->execute($invoice->id);

    expect($result['discount_percentage'])->toBe(0);
    expect($result['discount_amount'])->toBe('0.00');
    expect($result['items'])->toHaveCount(1);
    expect($result['items'][0]['amount'])->toBe('0.00');
    expect($result['items'][0]['sub_total'])->toBe('0.00');

    $amounts = collect($result['items'])->pluck('amount')->toArray();
    expect($amounts)->toContain('0.00');
});

test('handles large values correctly', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 9999.99,
        'discount_percentage' => 50,
        'discount_amount' => 1000.00,
        'status' => 0
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Premium Service',
        'quantity' => 100,
        'amount' => 99.99,
        'discount_percentage' => 10,
        'discount_amount' => 500.00,
        'sub_total' => 8499.10
    ]);

    $result = $this->interactor->execute($invoice->id);

    expect($result['discount_percentage'])->toBe(50);
    expect($result['discount_amount'])->toBe('1000.00');
    expect($result['items'])->toHaveCount(1);
    expect($result['items'][0]['quantity'])->toBe(100);
    expect($result['items'][0]['amount'])->toBe('99.99');
    expect($result['items'][0]['discount_percentage'])->toBe(10);
    expect($result['items'][0]['discount_amount'])->toBe('500.00');
    expect($result['items'][0]['sub_total'])->toBe('8499.10');

    $amounts = collect($result['items'])->pluck('amount')->toArray();
    expect($amounts)->toContain('99.99');
});

test('throws exception for non-existent invoice', function () {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';

    expect(fn() => $this->interactor->execute($fakeId))->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

test('includes all required invoice fields', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 100.00,
        'discount_percentage' => 10,
        'discount_amount' => 5.00,
        'status' => 1
    ]);

    // Create items with the expected amounts and discount percentages
    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    $result = $this->interactor->execute($invoice->id);

    expect($result['invoice_id'])->toBe($invoice->id);
    expect($result['invoice_no'])->toBe('INV0001');
    expect((string)$result['employee_no'])->toBe((string)$this->employee->id);
    expect($result['employee_name'])->toBe('John Employee');
    expect((string)$result['customer_no'])->toBe((string)$this->customer->id);
    expect($result['customer_name'])->toBe('Jane Customer');
    expect($result['token_no'])->toBe('INV0001');
    expect($result['discount_percentage'])->toBe(10);
    expect($result['discount_amount'])->toBe('5.00');
    expect($result['items'])->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);

    $amounts = collect($result['items'])->pluck('amount')->toArray();
    expect(in_array('25.00', $amounts) || in_array(25.00, $amounts))->toBeTrue();
    expect(in_array('50.00', $amounts) || in_array(50.00, $amounts))->toBeTrue();
    $discountPercentages = collect($result['items'])->pluck('discount_percentage')->toArray();
    expect(in_array(10, $discountPercentages) || in_array('10', $discountPercentages))->toBeTrue();
    expect(in_array(0, $discountPercentages) || in_array('0', $discountPercentages))->toBeTrue();
});

test('handles invoice with all discount types', function () {
    $invoice = Invoice::create([
        'invoice_no' => 'INV0001',
        'employee_no' => $this->employee->id,
        'customer_no' => $this->customer->id,
        'grand_total' => 200.00,
        'discount_percentage' => 15,
        'discount_amount' => 20.00,
        'status' => 0
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 2,
        'amount' => 50.00,
        'discount_percentage' => 10,
        'discount_amount' => 5.00,
        'sub_total' => 85.00
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 100.00,
        'discount_percentage' => 0,
        'discount_amount' => 15.00,
        'sub_total' => 85.00
    ]);

    $result = $this->interactor->execute($invoice->id);

    expect($result['discount_percentage'])->toBe(15);
    expect($result['discount_amount'])->toBe('20.00');
    expect($result['items'])->toHaveCount(2);

    $discountPercentages = collect($result['items'])->pluck('discount_percentage')->toArray();
    expect(in_array(10, $discountPercentages) || in_array('10', $discountPercentages))->toBeTrue();
    expect(in_array(0, $discountPercentages) || in_array('0', $discountPercentages))->toBeTrue();
}); 