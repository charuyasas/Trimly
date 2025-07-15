<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
