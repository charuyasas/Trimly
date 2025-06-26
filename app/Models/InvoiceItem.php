<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
   use SoftDeletes;

    protected $guarded = [];
    protected $dates = ['deleted_at'];

     public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
