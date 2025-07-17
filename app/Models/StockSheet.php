<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StockSheet extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = [
        'item_code',
        'ledger_code',
        'description',
        'reference_type',
        'reference_id',
        'credit',
        'debit'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    const STATUS = [
        'Employee Issue' => 'Employee Issue',
        'Employee Consumption' => 'Employee Consumption',
        'GRN' => 'GRN',
    ];

    // Auto-generate UUID
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
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
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

    public function items(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_code', 'id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ledger_code', 'ledger_code');
    }
}
