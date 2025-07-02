<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'employee_id', 'service_id',
        'booking_date', 'start_time', 'end_time',
        'status', 'notes'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function customer() { return $this->belongsTo(Customer::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function service() { return $this->belongsTo(Service::class); }
}
