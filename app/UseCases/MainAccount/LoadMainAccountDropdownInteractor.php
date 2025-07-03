<?php

namespace App\UseCases\MainAccount;

use App\Models\MainAccount;

class LoadMainAccountDropdownInteractor
{
    public function execute($search)
    {
        return MainAccount::where('main_code', 'like', "%$search%")
                        ->orWhere('main_account', 'like', "%$search%")
                        ->limit(10)
                        ->orderBy('main_code', 'asc')
                        ->get()
            ->map(fn($mainAccount) => [
                'label' => $mainAccount->main_code . ' - ' . $mainAccount->main_account,
                'value' => $mainAccount->main_code
            ]);
    }
}
