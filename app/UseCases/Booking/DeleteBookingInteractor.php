<?php

namespace App\UseCases\Booking;

use App\Models\Booking;

class DeleteBookingInteractor
{
    public function execute(Booking $booking)
    {
        return $booking->delete();
    }
}
