<?php

namespace App\UseCases\Booking;

use App\Models\Booking;
use App\UseCases\Booking\Requests\BookingRequest;

class StoreBookingInteractor
{
    public function execute(BookingRequest $bookingData)
    {
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
                ->orWhere(function ($q) use ($bookingData) {
                    $q->where('start_time', '<', $bookingData->start_time)
                        ->where('end_time', '>', $bookingData->end_time);
                });
            })->exists();

        if ($conflictExists) {
            return ['error' => true];
        }

        return Booking::create($bookingData->toArray())->load(['customer', 'employee', 'service']);
    }

}
