<?php

namespace App\UseCases\Grn;

use App\Models\Grn;

class LoadGrnDropdownInteractor
{
    public function execute($search)
    {
        return Grn::where('grn_number', 'like', "%$search%")
            ->limit(10)
            ->orderBy('grn_number', 'asc')
            ->get()
            ->map(fn($grn) => [
                'label' => $grn->grn_number,
                'value' => $grn->id
            ]);
    }
}
