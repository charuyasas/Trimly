<?php

namespace App\UseCases\Booking;

use App\Models\Booking;
use App\UseCases\Booking\Requests\BookingRequest;

class StoreBookingInteractor
{
    public function execute(BookingRequest $data)
  {
    if (!$data->status) {
        $data->status = 'pending'; // Fallback if not passed
    }

    // Time conflict check 
    $exists = Booking::where('employee_id', $data->employee_id)
        ->where('booking_date', $data->booking_date)
        ->where('status', '!=', 'cancelled')
        ->where(function ($q) use ($data) {
            $q->whereBetween('start_time', [$data->start_time, $data->end_time])
              ->orWhereBetween('end_time', [$data->start_time, $data->end_time])
              ->orWhere(function ($q) use ($data) {
                  $q->where('start_time', '<', $data->start_time)
                    ->where('end_time', '>', $data->end_time);
              });
        })->exists();

    if ($exists) {
        return ['error' => true];
    }

    return Booking::create($data->toArray())->load(['customer', 'employee', 'service']);
  }

}
