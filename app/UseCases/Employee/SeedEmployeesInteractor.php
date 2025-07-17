<?php

namespace App\UseCases\Employee;

use App\Models\Employee;
use App\UseCases\PostingAccount\StorePostingAccountInteractor;
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;

class SeedEmployeesInteractor
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

            Employee::factory()->create([
                'ledger_code' => $ledgerCode,
            ]);
        }
    }

    protected function createLedgerCode(): string
    {
        $data = [
            'posting_code'     => null,
            'posting_account'  => 'Employee Account',
            'main_code'        => 4,
            'heading_code'     => 8,
            'title_code'       => 9,
        ];

        $account = $this->postingAccountInteractor->execute(
            PostingAccountRequest::validateAndCreate($data)
        );

        return $account['ledger_code'];
    }
}
