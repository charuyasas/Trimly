<?php
namespace App\UseCases\PostingAccount;

use App\Models\PostingAccount;

 class ListPostingAccountInteractor {

    public function execute(){
        return PostingAccount::query()
        ->select([
            'posting_accounts.posting_code',
            'posting_accounts.posting_account',
            'posting_accounts.ledger_code',
            'main_accounts.main_account',
            'heading_accounts.heading_account',
            'title_accounts.title_account',
            ])
            ->join('main_accounts', 'posting_accounts.main_code', '=', 'main_accounts.main_code')
            ->join('heading_accounts', 'posting_accounts.heading_code', '=', 'heading_accounts.heading_code')
            ->join('title_accounts', 'posting_accounts.title_code', '=', 'title_accounts.title_code')
            ->orderBy('posting_accounts.created_at', 'asc')
            ->get();
    }

 }
