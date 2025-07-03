<?php

namespace App\UseCases\TitleAccount;

use App\Models\TitleAccount;

class LoadTitleAccountDropdownInteractor
{
    public function execute($search, $mainAcc, $headingAcc)
    {
        return TitleAccount::where(function ($query) use ($search) {
                $query->where('title_code', 'like', "%$search%")
                    ->orWhere('title_account', 'like', "%$search%");
            })
            ->where('main_code', '=', $mainAcc)
            ->where('heading_code', '=', $headingAcc)
            ->limit(10)
            ->orderBy('title_code', 'asc')
            ->get()
            ->map(fn($titleAccount) => [
                'label' => $titleAccount->title_code . ' - ' . $titleAccount->title_account,
                'value' => $titleAccount->title_code
            ]);
    }
}
