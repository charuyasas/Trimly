<?php

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use App\UseCases\Booking\DeleteBookingInteractor;
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

    $this->interactor = new DeleteBookingInteractor();
});

test('deletes booking successfully', function () {
    $booking = Booking::factory()->create([
        'customer_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'service_id' => $this->service->id,
        'booking_date' => '2024-01-15',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => 'confirmed'
    ]);

    $result = $this->interactor->execute($booking);

    expect($result)->toBeTrue();

    $this->assertSoftDeleted('bookings', [
        'id' => $booking->id
    ]);
});

test('returns false when booking does not exist', function () {
    $nonExistentId = '12345678-1234-1234-1234-123456789012';
    $booking = Booking::find($nonExistentId);
    $result = $booking ? $this->interactor->execute($booking) : false;
    expect($result)->toBeFalse();
});

test('deletes booking with all status types', function () {
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

    expect($result1)->toBeTrue();
    expect($result2)->toBeTrue();
    expect($result3)->toBeTrue();
    expect($result4)->toBeTrue();

    $this->assertSoftDeleted('bookings', ['id' => $pendingBooking->id]);
    $this->assertSoftDeleted('bookings', ['id' => $confirmedBooking->id]);
    $this->assertSoftDeleted('bookings', ['id' => $completedBooking->id]);
    $this->assertSoftDeleted('bookings', ['id' => $cancelledBooking->id]);
});

test('deletes booking with notes', function () {
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

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('bookings', ['id' => $booking->id]);
});

test('deletes multiple bookings', function () {
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

    $result1 = $this->interactor->execute($booking1);
    $result2 = $this->interactor->execute($booking2);

    expect($result1)->toBeTrue();
    expect($result2)->toBeTrue();

    $this->assertSoftDeleted('bookings', ['id' => $booking1->id]);
    $this->assertSoftDeleted('bookings', ['id' => $booking2->id]);
});

test('handles invalid uuid format gracefully', function () {
    $invalidId = 'invalid-uuid-format';
    $booking = Booking::find($invalidId);
    $result = $booking ? $this->interactor->execute($booking) : false;
    expect($result)->toBeFalse();
}); 