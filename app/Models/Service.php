<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Service extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'description', 'price'];
    protected $dates = ['deleted_at'];
}

