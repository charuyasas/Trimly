<?php

namespace App\UseCases\Booking;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\SMS\Contracts\SmsServiceInterface;
use App\UseCases\Booking\Requests\BookingRequest;
use Carbon\Carbon;

class StoreBookingInteractor
{
    public function execute(BookingRequest $bookingData)
    {
        // Default status
        if (!$bookingData->status) {
            $bookingData->status = 'pending';
        }

        // Time conflict check
        $conflictExists = Booking::where('employee_id', $bookingData->employee_id)
            ->where('booking_date', $bookingData->booking_date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($bookingData) {
                $q->whereBetween('start_time', [$bookingData->start_time, $bookingData->end_time])
                    ->orWhereBetween('end_time', [$bookingData->start_time, $bookingData->end_time])
                    ->orWhere(function ($q2) use ($bookingData) {
                        $q2->where('start_time', '<', $bookingData->start_time)
                            ->where('end_time', '>', $bookingData->end_time);
                    });
            })
            ->exists();

        if ($conflictExists) {
            return ['error' => true];
        }

        // Ensure service_ids is an array
        $servicesArray = is_array($bookingData->service_ids)
            ? $bookingData->service_ids
            : json_decode($bookingData->service_ids, true);

        // Create booking
        $booking = Booking::create(array_merge($bookingData->toArray(), [
            'service_ids' => $servicesArray
        ]));

        // Load customer & employee relationships
        $booking->load(['customer', 'employee']);

        // Attach services collection for JSON response
        $booking->services_collection = Service::whereIn('id', $servicesArray)->get();

        $bookingMessage = $this->generateBookingMessage($booking);

        $customer = Customer::findOrFail($bookingData->customer_id);

        $rawPhone = $customer->phone;
        $formattedPhone = $this->formatPhoneNumber($rawPhone);

        $smsService = app(SmsServiceInterface::class);
        $smsService->send($formattedPhone, $bookingMessage, [
            'mask' => 'DreamBarber',
            'campaign_name' => 'Dream Barber ' . now()->format('Y-m-d')
        ]);

        return $booking;
    }

    private function generateBookingMessage(Booking $booking): string
    {
        $bookingDate     = now()->format('Y-m-d'); // When booking was made
        $appointmentDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
        $startTime       = Carbon::parse($booking->start_time)->format('H:i');
        $endTime         = Carbon::parse($booking->end_time)->format('H:i');
        $employeeName    = $booking->employee->name ?? 'Staff';

        // Format services list
        $services = collect($booking->services_collection ?? [])->map(function ($service) {
            return "- {$service->description}";
        })->implode("\n");

        $message = <<<MSG
Booking Confirmation

Appointment Date: {$appointmentDate}
Time: {$startTime} - {$endTime}
Employee: {$employeeName}
Services:
{$services}

Thank you for booking with us!
MSG;

        return $message;
    }

    private function formatPhoneNumber(string $localNumber): string
    {
        $cleaned = preg_replace('/\D/', '', $localNumber);

        if (str_starts_with($cleaned, '0')) {
            return '94' . substr($cleaned, 1);
        }

        return $cleaned;
    }

}
