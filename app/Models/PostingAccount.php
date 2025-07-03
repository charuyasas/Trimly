<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostingAccount extends Model
{
    use SoftDeletes;


    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $dates = ['deleted_at'];

    protected $primaryKey = 'posting_code';

    public function getRouteKeyName()
    {
        return 'posting_code';
    }

    public function mainAcc()
    {
        return $this->belongsTo(MainAccount::class, 'main_code', 'main_code');
    }

    public function headingAcc()
    {
        return $this->belongsTo(HeadingAccount::class, 'heading_code', 'heading_code');
    }

    public function titleAcc()
    {
        return $this->belongsTo(TitleAccount::class, 'title_code', 'title_code');
    }

    protected static function booted()
    {
        static::created(function ($account) {
            $account->update([
                'ledger_code' => "{$account->main_code}-{$account->heading_code}-{$account->title_code}-{$account->posting_code}"
            ]);
        });

        static::updating(function ($account) {
            $account->ledger_code = "{$account->main_code}-{$account->heading_code}-{$account->title_code}-{$account->posting_code}";
        });
    }
}
