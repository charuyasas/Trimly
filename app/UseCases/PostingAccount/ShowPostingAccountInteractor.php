<?php

namespace App\UseCases\PostingAccount;

use App\Models\PostingAccount;

class ShowPostingAccountInteractor
{
    public function execute(PostingAccount $postingAccount): array
    {
        $postingAccount->load(['mainAccount', 'headingAccount', 'titleAccount']);

        return [
            'main_code'        => $postingAccount->main_code,
            'main_account'     => $postingAccount->main_code . ' - ' . ($postingAccount->mainAccount?->main_account ?? ''),

            'heading_code'     => $postingAccount->heading_code,
            'heading_account'  => $postingAccount->heading_code . ' - ' . ($postingAccount->headingAccount?->heading_account ?? ''),

            'title_code'       => $postingAccount->title_code,
            'title_account'    => $postingAccount->title_code . ' - ' . ($postingAccount->titleAccount?->title_account ?? ''),

            'posting_code'     => $postingAccount->posting_code,
            'posting_account'  => $postingAccount->posting_account,
        ];
    }
}
