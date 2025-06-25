<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'customer_id', 'employee_id', 'service_id',
        'booking_date', 'start_time', 'end_time',
        'status', 'notes'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function service() {
        return $this->belongsTo(Service::class);
    }
}

