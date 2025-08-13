<?php

namespace App\UseCases\PostingAccount;

use App\Models\MainAccount;

class GetAccountStructureInteractor
{
    public function execute(): array
    {
        return MainAccount::with([
            'headingAccounts' => function ($query) {
                $query->with([
                    'titleAccounts' => function ($query) {
                        $query->with('postingAccounts');
                    }
                ]);
            }
        ])->get()->toArray();
    }
}
