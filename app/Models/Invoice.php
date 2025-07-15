<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $dates = ['deleted_at'];

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

    const STATUS = [
        'PENDING' => 0,
        'FINISH' => 1,
    ];

    public function items(){
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_no', 'id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_no', 'id');
    }

}
