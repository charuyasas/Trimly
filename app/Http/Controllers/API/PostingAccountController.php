<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PostingAccount;
use App\UseCases\PostingAccount\DeletePostingAccountInteractor;
use App\UseCases\PostingAccount\ListPostingAccountInteractor;
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;
use App\UseCases\PostingAccount\ShowPostingAccountInteractor;
use App\UseCases\PostingAccount\StorePostingAccountInteractor;
use App\UseCases\PostingAccount\updatePostingAccountInteractor;

class PostingAccountController extends Controller
{
    public function index(ListPostingAccountInteractor $listPostingAccountInteractor)
    {
        return $listPostingAccountInteractor->execute();
    }

    public function store(StorePostingAccountInteractor $storePostingAccountInteractor)
    {
        $newPostingAccount = $storePostingAccountInteractor->execute(PostingAccountRequest::validateAndCreate(request()));
        return response()->json($newPostingAccount , 201);
    }

    public function show(PostingAccount $postingAccount, ShowPostingAccountInteractor $showPostingAccountInteractor)
    {
        return $showPostingAccountInteractor->execute($postingAccount);
    }

    public function update(PostingAccount $postingAccount, UpdatePostingAccountInteractor $updatePostingAccountInteractor)
    {
        $updatePostingAccount = $updatePostingAccountInteractor->execute($postingAccount, PostingAccountRequest::validateAndCreate(request()));
        return response()->json($updatePostingAccount);
    }

    public function destroy(PostingAccount $postingAccount, DeletePostingAccountInteractor $deletePostingAccountInteractor)
    {
        $deletePostingAccountInteractor->execute($postingAccount);
        return response()->json(null, 204);
    }



}
