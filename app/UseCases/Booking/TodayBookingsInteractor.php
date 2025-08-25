<?php

namespace App\UseCases\Booking;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TodayBookingsInteractor
{
    public function execute(): Collection
    {
        $today = Carbon::today()->toDateString();

        $bookings = Booking::where('booking_date', $today)
            ->where('status','<>','cancelled')
            ->with(['customer', 'employee'])
            ->orderBy('start_time')
            ->get();

        return $bookings->map(function ($booking) {
            $booking->services_collection = Service::whereIn('id', $booking->service_ids ?? [])->get();
            $booking->time_period = $booking->start_time . ' - ' . $booking->end_time;
            return $booking;
        });
    }
}
