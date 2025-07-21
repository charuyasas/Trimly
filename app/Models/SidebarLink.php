<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SidebarLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'permission_name',
        'display_name',
        'url',
        'icon_path',
        'parent_id',
    ];

    public function children()
    {
        return $this->hasMany(SidebarLink::class, 'parent_id');
    }
}
