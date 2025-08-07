<?php

namespace App\UseCases\UserShift\Requests;

use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Data;

class ShiftDetailsRequest extends Data
{
    public ?string $id;

    #[SpatieRule('required')]
    public int $user_id;

    #[SpatieRule('nullable')]
    public ?int $shift_id;

    #[SpatieRule('boolean')]
    public bool $status = false;

    #[SpatieRule('nullable', 'date')]
    public ?string $shift_in_time = null;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public float $opening_cash_in_hand = 0.00;

    #[SpatieRule('nullable', 'date')]
    public ?string $shift_off_time = null;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public float $day_end_cash_in_hand = 0.00;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public float $total_daily_sales = 0.00;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public float $total_daily_expenses = 0.00;

    #[SpatieRule('nullable', 'numeric', 'min:0')]
    public float $cash_shortage = 0.00;
}
