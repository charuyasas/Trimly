<?php

namespace App\UseCases\Booking;

use App\Models\Booking;

class ListBookingInteractor
{
    public function execute()
    {
        return Booking::with(['customer', 'employee', 'service'])->get();
    }
}


