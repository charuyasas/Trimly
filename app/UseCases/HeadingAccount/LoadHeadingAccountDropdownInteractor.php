<?php

namespace App\UseCases\HeadingAccount;

use App\Models\HeadingAccount;

class LoadHeadingAccountDropdownInteractor
{
    public function execute($search, $mainAcc)
    {
        return HeadingAccount::where(function ($query) use ($search) {
                $query->where('heading_code', 'like', "%$search%")
                    ->orWhere('heading_account', 'like', "%$search%");
            })
            ->where('main_code', '=', $mainAcc)
            ->limit(10)
            ->orderBy('heading_code', 'asc')
            ->get()
            ->map(fn($headingAccount) => [
                'label' => $headingAccount->heading_code . ' - ' . $headingAccount->heading_account,
                'value' => $headingAccount->heading_code
            ]);
    }
}
