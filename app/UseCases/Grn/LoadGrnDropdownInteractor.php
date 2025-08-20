<?php

namespace App\UseCases\Grn;

use App\Models\Grn;

class LoadGrnDropdownInteractor
{
    public function execute($search)
    {
        $search = (string) $search;

        return Grn::query()
            ->where('status', false)
            ->when($search !== '', function ($q) use ($search) {
                $q->where('token_no', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn($grn) => [
                'label' => $grn->token_no,
                'value' => $grn->id,
            ]);
    }
}
