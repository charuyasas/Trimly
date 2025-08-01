<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * @method static create(mixed[] $toArray)
 */
class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['supplier_code', 'name', 'contact_no', 'email', 'address', 'ledger_code'];

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
}
