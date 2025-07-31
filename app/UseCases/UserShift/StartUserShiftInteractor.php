<?php

namespace App\UseCases\UserShift;

use App\Models\ShiftDetails;
use App\UseCases\UserShift\Requests\ShiftDetailsRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class StartUserShiftInteractor
{
    public function execute(ShiftDetailsRequest $shiftInDetailsRequest): ShiftDetails
    {
        $shiftDetail = new ShiftDetails();

        $shiftDetail->id = Str::uuid();
        $shiftDetail->user_id = $shiftInDetailsRequest->user_id;
        $shiftDetail->opening_cash_in_hand = $shiftInDetailsRequest->opening_cash_in_hand;
        $shiftDetail->shift_in_time = Carbon::now();

        $shiftDetail->save();

        return $shiftDetail;
    }
}
