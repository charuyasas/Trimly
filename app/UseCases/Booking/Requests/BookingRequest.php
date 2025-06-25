<?php

namespace App\UseCases\Booking\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\Validation\Required;

class BookingRequest extends Data
{
    public ?string $id;

    #[SpatieRule('sometimes','required', 'uuid', 'exists:customers,id')]
    public string $customer_id;

    #[SpatieRule('sometimes','required', 'uuid', 'exists:employees,id')]
    public string $employee_id;

    #[SpatieRule('sometimes','required', 'uuid', 'exists:services,id')]
    public string $service_id;

    #[SpatieRule('sometimes','required', 'date')]
    public string $booking_date;

    #[SpatieRule('sometimes','required','after_or_equal:07:00', 'before_or_equal:21:00')]
    public string $start_time;

    #[SpatieRule('sometimes','required', 'after:start_time','before_or_equal:21:00')]
    public string $end_time;

    #[SpatieRule('sometimes', 'required', 'in:pending,confirmed,completed,cancelled')]
    public ?string $status='pending';

    #[SpatieRule('sometimes','nullable', 'string')]
    public ?string $notes;
}
