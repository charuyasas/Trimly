<?php

namespace App\UseCases\PostingAccount;

use App\Models\PostingAccount;
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;

 class UpdatePostingAccountInteractor {

    public function execute(PostingAccount $postingAccount, PostingAccountRequest $postingAccountRequest){
        $postingAccount->update($postingAccountRequest->except('posting_code')->toArray());

        return $postingAccount;
    }

 }
