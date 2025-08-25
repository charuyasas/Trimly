<?php

namespace App\UseCases\Booking;

use App\Models\Booking;
use App\Models\Service;
use App\UseCases\Booking\Requests\BookingRequest;

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

        return $booking;
    }
}
