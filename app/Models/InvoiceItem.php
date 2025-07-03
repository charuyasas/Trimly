<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InvoiceItem extends Model
{

    protected $guarded = [];

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

     public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'item_id', 'id');
    }
}
