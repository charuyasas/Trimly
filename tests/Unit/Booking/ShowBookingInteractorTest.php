<?php

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use App\UseCases\Booking\ShowBookingInteractor;
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

    $this->interactor = new ShowBookingInteractor();
});

test('returns booking with relationships when booking exists', function () {
    $booking = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed',
        'notes' => 'Regular haircut appointment'
    ]);

    $result = $this->interactor->execute($booking);

    expect($result)->toBeInstanceOf(Booking::class);
    expect((string)$result->id)->toBe((string)$booking->id);
    expect((string)$result->customer_id)->toBe((string)$this->customer->id);
    expect((string)$result->employee_id)->toBe((string)$this->employee->id);
    expect((string)$result->service_id)->toBe((string)$this->service->id);
    expect($result->booking_date)->toBe('2024-01-15');
    expect($result->start_time)->toBe('10:00');
    expect($result->end_time)->toBe('11:00');
    expect($result->status)->toBe('confirmed');
    expect($result->notes)->toBe('Regular haircut appointment');

    // Check relationships are loaded
    expect($result->customer)->toBeInstanceOf(Customer::class);
    expect($result->employee)->toBeInstanceOf(Employee::class);
    expect($result->service)->toBeInstanceOf(Service::class);
    expect((string)$result->customer->id)->toBe((string)$this->customer->id);
    expect((string)$result->employee->id)->toBe((string)$this->employee->id);
    expect((string)$result->service->id)->toBe((string)$this->service->id);
});

test('returns null when booking does not exist', function () {
    $nonExistentId = '12345678-1234-1234-1234-123456789012';
    $booking = Booking::find($nonExistentId);
    $result = $booking ? $this->interactor->execute($booking) : null;
    expect($result)->toBeNull();
});

test('returns booking with all status types', function () {
    $pendingBooking = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'pending'
    ]);

    $confirmedBooking = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-16',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
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

    $result1 = $this->interactor->execute($pendingBooking);
    $result2 = $this->interactor->execute($confirmedBooking);
    $result3 = $this->interactor->execute($completedBooking);
    $result4 = $this->interactor->execute($cancelledBooking);

    expect($result1->status)->toBe('pending');
    expect($result2->status)->toBe('confirmed');
    expect($result3->status)->toBe('completed');
    expect($result4->status)->toBe('cancelled');
});

test('returns booking with notes when provided', function () {
    $booking = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed',
        'notes' => 'Customer prefers short haircut'
    ]);

    $result = $this->interactor->execute($booking);

    expect($result->notes)->toBe('Customer prefers short haircut');
});

test('returns booking with null notes when not provided', function () {
    $booking = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed',
        'notes' => null
    ]);

    $result = $this->interactor->execute($booking);

    expect($result->notes)->toBeNull();
}); 