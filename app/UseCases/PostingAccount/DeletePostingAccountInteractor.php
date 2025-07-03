<?php
namespace App\UseCases\PostingAccount;

use App\Models\PostingAccount;

 class DeletePostingAccountInteractor {

    public function execute(PostingAccount $postingAccount){
        return $postingAccount->delete();
    }

 }
