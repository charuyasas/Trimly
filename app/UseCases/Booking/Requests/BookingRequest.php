<?php

namespace App\UseCases\Booking\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\Validation\Required;

class BookingRequest extends Data
{
    public ?string $id;

    #[SpatieRule('required', 'uuid', 'exists:customers,id')]
    public string $customer_id;

    #[SpatieRule('required', 'uuid', 'exists:employees,id')]
    public string $employee_id;

    #[SpatieRule('required', 'uuid', 'exists:services,id')]
    public string $service_id;

    #[SpatieRule('required', 'date')]
    public string $booking_date;

    #[SpatieRule('required')]
    public string $start_time;

    #[SpatieRule('required', 'after:start_time')]
    public string $end_time;

    #[SpatieRule('nullable', 'in:pending,confirmed,completed,cancelled')]
    public ?string $status = 'pending';

    #[SpatieRule('nullable', 'string')]
    public ?string $notes;
}
