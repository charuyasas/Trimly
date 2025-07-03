<?php

namespace App\UseCases\PostingAccount;

use App\Models\PostingAccount;

class ShowPostingAccountInteractor
{
    public function execute(PostingAccount $postingAccount): array
    {
        $postingAccount->load(['mainAcc', 'headingAcc', 'titleAcc']);

        return [
            'main_code'        => $postingAccount->main_code,
            'main_account'     => $postingAccount->main_code . ' - ' . ($postingAccount->mainAcc?->main_account ?? ''),

            'heading_code'     => $postingAccount->heading_code,
            'heading_account'  => $postingAccount->heading_code . ' - ' . ($postingAccount->headingAcc?->heading_account ?? ''),

            'title_code'       => $postingAccount->title_code,
            'title_account'    => $postingAccount->title_code . ' - ' . ($postingAccount->titleAcc?->title_account ?? ''),

            'posting_code'     => $postingAccount->posting_code,
            'posting_account'  => $postingAccount->posting_account,
        ];
    }
}
