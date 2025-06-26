<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{

    protected $guarded = [];

     public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
