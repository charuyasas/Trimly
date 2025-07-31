<?php

namespace App\UseCases\UserShift;

use App\Models\ShiftDetails;

class GetOngoingShiftUserDetailsInteractor
{
    public function execute(): ?string
    {
        $activeShift = ShiftDetails::where('status', true)->first();
        return $activeShift;
    }
}
