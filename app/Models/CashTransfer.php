<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CashTransfer extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $guarded;

    protected $dates = ['deleted_at'];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(PostingAccount::class, 'credit_account', 'ledger_code');
    }

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(PostingAccount::class, 'debit_account', 'ledger_code');
    }
}
