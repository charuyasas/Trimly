<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupplierPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'supplier_id',
        'payment_type',
        'amount',
        'payments',        // ✅ Add this
        'bank_slip_no',    // ✅ Add this if used
        'date',            // ✅ Add this if used
    ];

    protected $casts = [
        'amount' => 'float',
        'payments' => 'array', // ✅ Cast payments as array
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id'); // ✅ Corrected
    }
}

