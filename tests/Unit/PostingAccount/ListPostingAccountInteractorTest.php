<?php

use App\Models\PostingAccount;
use App\Models\MainAccount;
use App\Models\HeadingAccount;
use App\Models\TitleAccount;
use App\UseCases\PostingAccount\ListPostingAccountInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->mainAccount = MainAccount::factory()->create([
        'main_code' => 1,
        'main_account' => 'Assets'
    ]);

    $this->headingAccount = HeadingAccount::factory()->create([
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => 10,
        'heading_account' => 'Current Assets'
    ]);

    $this->titleAccount = TitleAccount::factory()->create([
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => 100,
        'title_account' => 'Cash and Cash Equivalents'
    ]);

    $this->interactor = new ListPostingAccountInteractor();
});

test('returns all posting accounts', function () {
    $postingAccount1 = PostingAccount::factory()->create([
        'posting_account' => 'Cash in Bank',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $postingAccount2 = PostingAccount::factory()->create([
        'posting_account' => 'Accounts Receivable',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(2);
    expect($result[0])->toBeInstanceOf(PostingAccount::class);
    expect($result[1])->toBeInstanceOf(PostingAccount::class);
    expect($result[0]->posting_account)->toBe('Cash in Bank');
    expect($result[1]->posting_account)->toBe('Accounts Receivable');
});

test('returns empty collection when no posting accounts exist', function () {
    $result = $this->interactor->execute();

    expect($result)->toBeEmpty();
    expect($result)->toHaveCount(0);
});

test('returns posting accounts with relationships loaded', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Cash in Bank',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(1);
    expect($result[0])->toBeInstanceOf(PostingAccount::class);
    expect($result[0]->main_account)->toBe('Assets');
    expect($result[0]->heading_account)->toBe('Current Assets');
    expect($result[0]->title_account)->toBe('Cash and Cash Equivalents');
});

test('returns posting accounts ordered by posting_code', function () {
    $postingAccount3 = PostingAccount::factory()->create([
        'posting_account' => 'Third Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $postingAccount1 = PostingAccount::factory()->create([
        'posting_account' => 'First Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $postingAccount2 = PostingAccount::factory()->create([
        'posting_account' => 'Second Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(3);
    expect($result[0]->posting_code)->toBeLessThan($result[1]->posting_code);
    expect($result[1]->posting_code)->toBeLessThan($result[2]->posting_code);
});

test('returns posting accounts with special characters in name', function () {
    $postingAccount1 = PostingAccount::factory()->create([
        'posting_account' => 'Cash & Bank – "Primary"',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $postingAccount2 = PostingAccount::factory()->create([
        'posting_account' => 'Accounts (Receivable)',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute();

    $postingAccounts = collect($result)->pluck('posting_account')->toArray();
    expect($postingAccounts)->toContain('Cash & Bank – "Primary"');
    expect($postingAccounts)->toContain('Accounts (Receivable)');
});

test('returns posting accounts with max length name', function () {
    $postingAccountName = str_repeat('a', 255);
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => $postingAccountName,
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(1);
    expect($result[0]->posting_account)->toBe($postingAccountName);
});

test('returns posting accounts with different code combinations', function () {
    $mainAccount2 = MainAccount::factory()->create(['main_code' => 2, 'main_account' => 'Liabilities']);
    $headingAccount2 = HeadingAccount::factory()->create([
        'main_code' => $mainAccount2->main_code,
        'heading_code' => 20,
        'heading_account' => 'Current Liabilities'
    ]);
    $titleAccount2 = TitleAccount::factory()->create([
        'main_code' => $mainAccount2->main_code,
        'heading_code' => $headingAccount2->heading_code,
        'title_code' => 200,
        'title_account' => 'Accounts Payable'
    ]);

    $postingAccount1 = PostingAccount::factory()->create([
        'posting_account' => 'Account One',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $postingAccount2 = PostingAccount::factory()->create([
        'posting_account' => 'Account Two',
        'main_code' => $mainAccount2->main_code,
        'heading_code' => $headingAccount2->heading_code,
        'title_code' => $titleAccount2->title_code
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(2);
    $accounts = collect($result)->map(fn($acc) => [
        'main_account' => $acc->main_account,
        'heading_account' => $acc->heading_account,
        'title_account' => $acc->title_account
    ])->toArray();
    expect($accounts)->toContain([
        'main_account' => 'Assets',
        'heading_account' => 'Current Assets',
        'title_account' => 'Cash and Cash Equivalents'
    ]);
    expect($accounts)->toContain([
        'main_account' => 'Liabilities',
        'heading_account' => 'Current Liabilities',
        'title_account' => 'Accounts Payable'
    ]);
});

test('returns posting accounts with generated ledger codes', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Test Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute();

    expect($result)->toHaveCount(1);
    expect($result[0]->ledger_code)->toBe("{$this->mainAccount->main_code}-{$this->headingAccount->heading_code}-{$this->titleAccount->title_code}-{$postingAccount->posting_code}");
}); 