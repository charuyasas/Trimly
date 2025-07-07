<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Grn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grn_number',
        'grn_date',
        'supplier_id',
        'supplier_invoice_number',
        'grn_type',
        'store_location',
        'note',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot(): void {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
}
