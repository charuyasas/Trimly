<?php

namespace App\UseCases\PostingAccount;

use App\Models\PostingAccount;
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;


 class StorePostingAccountInteractor {

    public function execute(PostingAccountRequest $employeeRequest){
        $employee = PostingAccount::create($employeeRequest->toArray());
        return $employee->toArray();
    }

 }
