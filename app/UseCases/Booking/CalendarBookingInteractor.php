<?php

namespace App\UseCases\Booking;

use App\Models\Booking;

class CalendarBookingInteractor
{
    public function execute()
    {
        return Booking::with(['customer', 'employee'])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'resourceId' => $b->employee_id,
                    'title' => $b->customer->name ?? 'Customer',
                    'start' => $b->booking_date . 'T' . $b->start_time,
                    'end' => $b->booking_date . 'T' . $b->end_time,
                    'status' => $b->status
                ];
            });
    }
}
