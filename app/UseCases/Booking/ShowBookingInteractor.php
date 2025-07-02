<?php

namespace App\UseCases\Booking;

use App\Models\Booking;

class ShowBookingInteractor
{
    public function execute(Booking $booking)
    {
        return $booking->load(['customer', 'employee', 'service']);
    }
}
