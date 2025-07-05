<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TitleAccount extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $guarded = [];

    protected $dates = ['deleted_at'];

    public function mainAccount() {
        return $this->belongsTo(MainAccount::class, 'main_code', 'main_code');
    }

    public function headingAccount() {
        return $this->belongsTo(HeadingAccount::class, 'heading_code', 'heading_code');
    }
}
