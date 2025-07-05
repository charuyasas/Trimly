<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostingAccount extends Model
{
    use SoftDeletes;
    use HasFactory;


    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'posting_code';

    public function mainAccount()
    {
        return $this->belongsTo(MainAccount::class, 'main_code', 'main_code');
    }

    public function headingAccount()
    {
        return $this->belongsTo(HeadingAccount::class, 'heading_code', 'heading_code');
    }

    public function titleAccount()
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
