<?php

namespace App\UseCases\UserShift;

use App\Models\ShiftDetails;

class LoadShiftIDDropdownInteractor
{
    public function execute()
    {
        // Fetch invoices
        $invoices = ShiftDetails::where('status', 1)
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn($inv) => [
                'label' => $inv->shift_id,
                'value' => $inv->shift_id,
            ]);

        return $invoices;
    }
}
