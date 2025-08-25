<?php

namespace App\UseCases\Booking;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

class ListBookingInteractor
{
    public function execute(): Collection
    {
        return Booking::with(['customer', 'employee', 'service'])->get();
    }
}


