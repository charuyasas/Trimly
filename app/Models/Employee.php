<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = ['employee_id', 'name', 'address', 'contact_no'];
    protected $dates = ['deleted_at'];
}
