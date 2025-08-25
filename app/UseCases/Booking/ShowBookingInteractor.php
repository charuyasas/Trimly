<?php

namespace App\UseCases\Booking;

use App\Models\Booking;
use App\Models\Service;

class ShowBookingInteractor
{
    public function execute(Booking $booking)
    {
        $booking->load(['customer', 'employee']);
        $booking->services_collection = Service::whereIn('id', $booking->service_ids ?? [])->get();

        return $booking;
    }
}
