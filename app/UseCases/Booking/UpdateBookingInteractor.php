<?php

namespace App\UseCases\Booking;

use App\Models\Booking;
use App\UseCases\Booking\Requests\BookingRequest;

class UpdateBookingInteractor
{
    public function execute(Booking $existingBooking, BookingRequest $requestData)
    {
        $fieldsToUpdate = $requestData->except('id')->toArray();

        $existingBooking->update($fieldsToUpdate);

        return $existingBooking->fresh()->load(['customer', 'employee', 'service']);
    }
}
