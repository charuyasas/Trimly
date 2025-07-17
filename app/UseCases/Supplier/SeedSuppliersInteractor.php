<?php

namespace App\UseCases\Supplier;

use App\Models\Supplier;
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;
use App\UseCases\PostingAccount\StorePostingAccountInteractor;

class SeedSuppliersInteractor
{
    protected StorePostingAccountInteractor $postingAccountInteractor;

    public function __construct(StorePostingAccountInteractor $postingAccountInteractor)
    {
        $this->postingAccountInteractor = $postingAccountInteractor;
    }

    public function execute(int $count = 50): void
    {
        for ($i = 0; $i < $count; $i++) {
            $ledgerCode = $this->createLedgerCode();

            Supplier::factory()->create([
                'ledger_code' => $ledgerCode,
            ]);
        }
    }

    protected function createLedgerCode(): string
    {
        $data = [
            'posting_code'     => null,
            'posting_account'  => 'Supplier Account',
            'main_code'        => 4,
            'heading_code'     => 9,
            'title_code'       => 10,
        ];

        $account = $this->postingAccountInteractor->execute(
            PostingAccountRequest::validateAndCreate($data)
        );

        return $account['ledger_code'];
    }
}
