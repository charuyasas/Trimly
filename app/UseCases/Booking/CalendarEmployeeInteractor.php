<?php

namespace App\UseCases\Booking;

use App\Models\Employee;

class CalendarEmployeeInteractor
{
    public function execute()
    {
        return Employee::select('id', 'name as title')->get();
    }
}
