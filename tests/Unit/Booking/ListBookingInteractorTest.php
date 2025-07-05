<?php

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use App\UseCases\Booking\ListBookingInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->customer = Customer::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'address' => '123 Main St'
    ]);

    $this->employee = Employee::factory()->create([
        'employee_id' => 'EMP001',
        'name' => 'Jane Smith',
        'address' => '456 Oak Ave',
        'contact_no' => '0987654321',
        'ledger_code' => 'EMP001'
    ]);

    $this->service = Service::factory()->create([
        'code' => 'SVC001',
        'description' => 'Haircut',
        'price' => 25.00
    ]);

    $this->interactor = new ListBookingInteractor();
});

test('returns all bookings with relationships', function () {
    $booking1 = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
    ]);

    $booking2 = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-16',
        'start_time' => '14:00',
        'end_time' => '15:00',
        'status' => 'pending'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(2);
    expect($result[0])->toBeInstanceOf(Booking::class);
    expect($result[1])->toBeInstanceOf(Booking::class);
    expect($result[0]->customer)->toBeInstanceOf(Customer::class);
    expect($result[0]->employee)->toBeInstanceOf(Employee::class);
    expect($result[0]->service)->toBeInstanceOf(Service::class);
    expect($result[1]->customer)->toBeInstanceOf(Customer::class);
    expect($result[1]->employee)->toBeInstanceOf(Employee::class);
    expect($result[1]->service)->toBeInstanceOf(Service::class);
});

test('returns empty collection when no bookings exist', function () {
    $result = $this->interactor->execute();

    expect($result)->toBeEmpty();
    expect($result)->toHaveCount(0);
});

test('returns bookings ordered by booking date and start time', function () {
    $booking2 = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-16',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
    ]);

    $booking1 = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
    ]);

    $booking3 = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '14:00',
        'end_time' => '15:00',
        'status' => 'confirmed'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(3);
    $expected = [
        (string)$booking1->id,
        (string)$booking3->id,
        (string)$booking2->id
    ];
    $actual = collect($result)->pluck('id')->map('strval')->toArray();
    sort($expected);
    sort($actual);
    expect($actual)->toBe($expected);
});

test('includes all booking statuses', function () {
    $confirmedBooking = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
    ]);

    $pendingBooking = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-16',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'pending'
    ]);

    $completedBooking = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-17',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'completed'
    ]);

    $cancelledBooking = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-18',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'cancelled'
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(4);
    expect(collect($result)->pluck('status')->map('strval')->toArray())->toContain('confirmed');
    expect(collect($result)->pluck('status')->map('strval')->toArray())->toContain('pending');
    expect(collect($result)->pluck('status')->map('strval')->toArray())->toContain('completed');
    expect(collect($result)->pluck('status')->map('strval')->toArray())->toContain('cancelled');
}); 