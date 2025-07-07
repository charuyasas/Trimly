<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Grn extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = (string) Str::uuid());
    }

    const STATUS = [
        0 => 'pending',
        1 => 'complete',
        2 => 'finish',
    ];

    public function items()
    {
        return $this->hasMany(GrnItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

