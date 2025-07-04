<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeadingAccount extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $dates = ['deleted_at'];

    public function mainAccount() {
        return $this->belongsTo(MainAccount::class, 'main_code', 'main_code');
    }
}
