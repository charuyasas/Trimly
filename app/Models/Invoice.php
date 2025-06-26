<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $dates = ['deleted_at'];

    const STATUS = [
        0 => 'pending',
        1 => 'complete',
        2 => 'finish',
    ];

    public function items(){
        return $this->hasMany(\App\Models\InvoiceItem::class, 'invoice_id');
    }

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_no', 'id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_no', 'id');
    }

}
