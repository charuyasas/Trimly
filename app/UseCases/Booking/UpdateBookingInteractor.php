<?php

namespace App\UseCases\Booking;

use App\Models\Booking;
use App\UseCases\Booking\Requests\BookingRequest;

class UpdateBookingInteractor
{
    public function execute(Booking $booking, BookingRequest $data)
    {
        $booking->update($data->except('id')->toArray());
        return $booking->fresh()->load(['customer', 'employee', 'service']);
    }
}
