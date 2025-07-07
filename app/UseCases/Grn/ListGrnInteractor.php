<?php

namespace App\UseCases\Grn;

use App\Models\Grn;
use Illuminate\Database\Eloquent\Collection;

class ListGrnInteractor
{
    public function execute(): Collection
    {
        return Grn::with(['items', 'supplier'])->latest()->get();
    }
}
