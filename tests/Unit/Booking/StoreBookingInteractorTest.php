<?php

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use App\UseCases\Booking\StoreBookingInteractor;
use App\UseCases\Booking\Requests\BookingRequest;
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

    $this->interactor = new StoreBookingInteractor();
});

test('creates a new booking successfully', function () {
    $bookingData = BookingRequest::from([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'notes' => 'Regular haircut appointment'
    ]);

    $result = $this->interactor->execute($bookingData);

    expect($result)->toBeInstanceOf(Booking::class);
    expect((string)$result->customer_id)->toBe((string)$this->customer->id);
    expect((string)$result->employee_id)->toBe((string)$this->employee->id);
    expect((string)$result->service_id)->toBe((string)$this->service->id);
    expect($result->booking_date)->toBe('2024-01-15');
    expect($result->start_time)->toBe('10:00');
    expect($result->end_time)->toBe('11:00');
    expect($result->status)->toBe('pending');
    expect($result->notes)->toBe('Regular haircut appointment');

    $this->assertDatabaseHas('bookings', [
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'pending'
    ]);
});

test('sets default status to pending when not provided', function () {
    $bookingData = BookingRequest::from([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00'
    ]);

    $result = $this->interactor->execute($bookingData);

    expect($result->status)->toBe('pending');
});

test('returns error when time conflict exists with overlapping start time', function () {
    // Create existing booking
    Booking::factory()->create([
        'employee_id' => $this->employee->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
    ]);

    // Try to create conflicting booking
    $bookingData = BookingRequest::from([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:30',
        'end_time' => '11:30'
    ]);

    $result = $this->interactor->execute($bookingData);

    expect($result)->toBe(['error' => true]);
});

test('returns error when time conflict exists with overlapping end time', function () {
    // Create existing booking
    Booking::factory()->create([
        'employee_id' => $this->employee->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
    ]);

    // Try to create conflicting booking
    $bookingData = BookingRequest::from([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '09:30',
        'end_time' => '10:30'
    ]);

    $result = $this->interactor->execute($bookingData);

    expect($result)->toBe(['error' => true]);
});

test('returns error when time conflict exists with completely overlapping booking', function () {
    // Create existing booking
    Booking::factory()->create([
        'employee_id' => $this->employee->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
    ]);

    // Try to create conflicting booking
    $bookingData = BookingRequest::from([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '09:30',
        'end_time' => '11:30'
    ]);

    $result = $this->interactor->execute($bookingData);

    expect($result)->toBe(['error' => true]);
});

test('allows booking when existing booking is cancelled', function () {
    // Create cancelled booking
    Booking::factory()->create([
        'employee_id' => $this->employee->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'cancelled'
    ]);

    // Try to create booking at same time
    $bookingData = BookingRequest::from([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00'
    ]);

    $result = $this->interactor->execute($bookingData);

    expect($result)->toBeInstanceOf(Booking::class);
    expect($result->status)->toBe('pending');
});

test('allows booking for different employees on same time', function () {
    $employee2 = Employee::factory()->create([
        'employee_id' => 'EMP002',
        'name' => 'Bob Johnson',
        'address' => '789 Pine St',
        'contact_no' => '5555555555',
        'ledger_code' => 'EMP002'
    ]);

    // Create booking for first employee
    Booking::factory()->create([
        'employee_id' => $this->employee->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
    ]);

    // Try to create booking for second employee at same time
    $bookingData = BookingRequest::from([
        'customer_id' => $this->customer->id,
        'employee_id' => $employee2->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00'
    ]);

    $result = $this->interactor->execute($bookingData);

    expect($result)->toBeInstanceOf(Booking::class);
    expect((string)$result->employee_id)->toBe((string)$employee2->id);
});

test('allows booking for different dates on same time', function () {
    // Create booking for different date
    Booking::factory()->create([
        'employee_id' => $this->employee->id,
        'booking_date' => '2024-01-16',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
    ]);

    // Try to create booking for same employee at same time but different date
    $bookingData = BookingRequest::from([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00'
    ]);

    $result = $this->interactor->execute($bookingData);

    expect($result)->toBeInstanceOf(Booking::class);
    expect($result->booking_date)->toBe('2024-01-15');
});

test('loads relationships when creating booking', function () {
    $bookingData = BookingRequest::from([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00'
    ]);

    $result = $this->interactor->execute($bookingData);

    expect($result->customer)->toBeInstanceOf(Customer::class);
    expect($result->employee)->toBeInstanceOf(Employee::class);
    expect($result->service)->toBeInstanceOf(Service::class);
    expect($result->customer->id)->toBe($this->customer->id);
    expect((string)$result->employee->id)->toBe((string)$this->employee->id);
    expect((string)$result->service->id)->toBe((string)$this->service->id);
}); 