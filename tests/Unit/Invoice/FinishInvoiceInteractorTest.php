<?php

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Customer;
use App\UseCases\Invoice\FinishInvoiceInteractor;
use App\UseCases\Invoice\Requests\InvoiceRequest;
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

    $this->interactor = new FinishInvoiceInteractor();
});

test('finishes invoice with percentage discount', function () {
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
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    $request = InvoiceRequest::from([
        'discount_percentage' => 10,
        'discount_amount' => 0,
        'items' => []
    ]);

    $result = $this->interactor->execute($invoice->id, $request);

    expect($result['message'])->toBe('Invoice finalized successfully.');

    // Total: 50 + 50 = 100
    // After 10% discount: 100 * 0.9 = 90.00
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'grand_total' => 90.00,
        'status' => 1
    ]);
});

test('finishes invoice with amount discount', function () {
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
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    $request = InvoiceRequest::from([
        'discount_percentage' => 0,
        'discount_amount' => 15.00,
        'items' => []
    ]);

    $result = $this->interactor->execute($invoice->id, $request);

    expect($result['message'])->toBe('Invoice finalized successfully.');

    // Total: 50 + 50 = 100
    // After 15.00 discount: 100 - 15 = 85.00
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'grand_total' => 85.00,
        'status' => 1
    ]);
});

test('prioritizes percentage discount over amount discount', function () {
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
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 50.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 50.00
    ]);

    $request = InvoiceRequest::from([
        'discount_percentage' => 20,
        'discount_amount' => 15.00, // This should be ignored
        'items' => []
    ]);

    $result = $this->interactor->execute($invoice->id, $request);

    expect($result['message'])->toBe('Invoice finalized successfully.');

    // Total: 50 + 50 = 100
    // After 20% discount: 100 * 0.8 = 80.00 (not 100 - 15 = 85.00)
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'grand_total' => 80.00,
        'status' => 1
    ]);
});

test('handles zero discount correctly', function () {
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
        'quantity' => 2,
        'amount' => 25.00,
        'discount_percentage' => 0,
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

    $request = InvoiceRequest::from([
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'items' => []
    ]);

    $result = $this->interactor->execute($invoice->id, $request);

    expect($result['message'])->toBe('Invoice finalized successfully.');

    // Total: 50 + 50 = 100
    // No discount: 100 - 0 = 100.00
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'grand_total' => 100.00,
        'status' => 1
    ]);
});

test('prevents negative grand total', function () {
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
        'quantity' => 1,
        'amount' => 25.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 25.00
    ]);

    $request = InvoiceRequest::from([
        'discount_amount' => 100.00, // More than the total
        'items' => []
    ]);

    $result = $this->interactor->execute($invoice->id, $request);

    expect($result['message'])->toBe('Invoice finalized successfully.');

    // Total: 25
    // After 100.00 discount: max(0, 25 - 100) = 0.00
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'grand_total' => 0.00,
        'status' => 1
    ]);
});

test('handles non-existent invoice', function () {
    $fakeId = '550e8400-e29b-41d4-a716-446655440000';

    $request = InvoiceRequest::from([
        'discount_percentage' => 10,
        'items' => []
    ]);

    $result = $this->interactor->execute($fakeId, $request);

    expect($result['message'])->toBe('Failed to finish invoice.')
        ->and($result)->toHaveKey('error');
});

test('calculates discount amount from percentage correctly', function () {
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
        'discount_percentage' => 0,
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

    $request = InvoiceRequest::from([
        'discount_percentage' => 15,
        'items' => []
    ]);

    $result = $this->interactor->execute($invoice->id, $request);

    expect($result['message'])->toBe('Invoice finalized successfully.');

    // Total: 50 + 50 = 100
    // 15% discount: 100 * 0.15 = 15.00 discount amount
    // Final total: 100 - 15 = 85.00
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'grand_total' => 85.00,
        'status' => 1
    ]);
});

test('rounds grand total to 2 decimal places', function () {
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
        'quantity' => 1,
        'amount' => 33.33,
        'sub_total' => 33.33
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service2->id,
        'item_description' => 'Hair Coloring',
        'quantity' => 1,
        'amount' => 33.33,
        'sub_total' => 33.33
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'item_id' => $this->service1->id,
        'item_description' => 'Haircut Service',
        'quantity' => 1,
        'amount' => 33.34,
        'sub_total' => 33.34
    ]);

    $request = InvoiceRequest::from([
        'discount_percentage' => 10,
        'items' => []
    ]);

    $result = $this->interactor->execute($invoice->id, $request);

    expect($result['message'])->toBe('Invoice finalized successfully.');

    // Total: 33.33 + 33.33 + 33.34 = 100
    // After 10% discount: 100 * 0.9 = 90.00
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'grand_total' => 90.00,
        'status' => 1
    ]);
});

test('hides created_at and updated_at from response', function () {
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
        'quantity' => 1,
        'amount' => 25.00,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'sub_total' => 25.00
    ]);

    $request = InvoiceRequest::from([
        'discount_percentage' => 10,
        'items' => []
    ]);

    $result = $this->interactor->execute($invoice->id, $request);

    expect($result['message'])->toBe('Invoice finalized successfully.')
        ->and($result['invoice'])->toHaveKey('id')
        ->and($result['invoice'])->not->toHaveKey('created_at')
        ->and($result['invoice'])->not->toHaveKey('updated_at');
});
