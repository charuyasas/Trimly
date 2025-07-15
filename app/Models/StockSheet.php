<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StockSheet extends Model
{
    use HasFactory;

    protected $guarded = [];

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

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_code', 'code');
    }

}
