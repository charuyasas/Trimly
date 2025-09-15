<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class JournalEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    const STATUS = [
        'Sale' => 'Sale',
        'Purchase' => 'Purchase',
        'Inventory' => 'Inventory',
        'GRN' => 'GRN',
        'Expenses' => 'Expenses',
        'CashTransfer' => 'Cash Transfer',
        'SupplierPayment' => 'Supplier Payment',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected static function booted(): void
    {
        static::creating(function ($stockSheet) {
            $stockSheet->created_by = auth()->id();
            $stockSheet->updated_by = auth()->id();
        });

        static::updating(function ($stockSheet) {
            $stockSheet->updated_by = auth()->id();
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
