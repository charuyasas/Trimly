<?php

namespace App\UseCases\PostingAccount;

use App\Models\PostingAccount;
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;


 class StorePostingAccountInteractor {

    public function execute(PostingAccountRequest $postingAccountRequest){
        $postingAccount = PostingAccount::create($postingAccountRequest->toArray());
        return $postingAccount->toArray();
    }

 }
