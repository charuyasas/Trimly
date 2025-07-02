<?php

namespace App\UseCases\Booking\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;

class UpdateBookingStatusRequest extends Data
{
    #[SpatieRule('required', 'in:pending,confirmed,completed,cancelled')]
    public string $status;
}
