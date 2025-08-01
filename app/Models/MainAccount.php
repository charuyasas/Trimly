<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MainAccount extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $guarded = [];

    protected $dates = ['deleted_at'];
}
