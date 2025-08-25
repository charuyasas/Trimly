<?php

namespace App\UseCases\Booking;

use App\Models\Booking;
use App\Models\Service;
use App\UseCases\Booking\Requests\BookingRequest;

class UpdateBookingInteractor
{
    public function execute(Booking $existingBooking, BookingRequest $requestData)
    {
        $fieldsToUpdate = collect($requestData->toArray())
            ->filter(fn ($value, $key) => request()->has($key))
            ->except('id')
            ->toArray();

        if (isset($fieldsToUpdate['service_ids']) && !is_array($fieldsToUpdate['service_ids'])) {
            $fieldsToUpdate['service_ids'] = json_decode($fieldsToUpdate['service_ids'], true);
        }

        $existingBooking->update($fieldsToUpdate);
        $existingBooking->load(['customer', 'employee']);
        $existingBooking->services_collection = Service::whereIn('id', $existingBooking->service_ids ?? [])->get();

        return $existingBooking;
    }
}
