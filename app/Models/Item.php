<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code', 'description', 'rack_location',
        'supplier_id', 'category_id', 'sub_category_id',
        'measure_unit', 'is_active',
        'list_price', 'retail_price', 'wholesale_price',
        'average_cost'
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

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function subCategory() {
        return $this->belongsTo(SubCategory::class);
    }
}

