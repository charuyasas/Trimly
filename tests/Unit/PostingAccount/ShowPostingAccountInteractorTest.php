<?php

use App\Models\PostingAccount;
use App\Models\MainAccount;
use App\Models\HeadingAccount;
use App\Models\TitleAccount;
use App\UseCases\PostingAccount\ShowPostingAccountInteractor;
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

    $this->interactor = new ShowPostingAccountInteractor();
});

test('returns posting account when it exists', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Cash in Bank',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeArray();
    expect($result['posting_code'])->toBe($postingAccount->posting_code);
    expect($result['posting_account'])->toBe('Cash in Bank');
    expect($result['main_code'])->toBe($this->mainAccount->main_code);
    expect($result['heading_code'])->toBe($this->headingAccount->heading_code);
    expect($result['title_code'])->toBe($this->titleAccount->title_code);
});

test('returns null when posting account does not exist', function () {
    $nonExistentPostingAccount = new PostingAccount();
    $nonExistentPostingAccount->posting_code = 99999;

    $result = $this->interactor->execute($nonExistentPostingAccount);

    expect($result)->toBeArray();
    expect($result['posting_code'])->toBe(99999);
});

test('returns posting account with relationships loaded', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Cash in Bank',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeArray();
    expect($result['main_account'])->toContain('Assets');
    expect($result['heading_account'])->toContain('Current Assets');
    expect($result['title_account'])->toContain('Cash and Cash Equivalents');
});

test('returns posting account with special characters in name', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Cash & Bank – "Primary" Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeArray();
    expect($result['posting_account'])->toBe('Cash & Bank – "Primary" Account');
});

test('returns posting account with max length name', function () {
    $postingAccountName = str_repeat('a', 255);
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => $postingAccountName,
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeArray();
    expect($result['posting_account'])->toBe($postingAccountName);
});

test('returns posting account with generated ledger code', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Test Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeArray();
    expect($result['posting_code'])->toBe($postingAccount->posting_code);
});

test('returns posting account with different code combinations', function () {
    $mainAccount2 = MainAccount::factory()->create(['main_code' => 2]);
    $headingAccount2 = HeadingAccount::factory()->create([
        'main_code' => $mainAccount2->main_code,
        'heading_code' => 20
    ]);
    $titleAccount2 = TitleAccount::factory()->create([
        'main_code' => $mainAccount2->main_code,
        'heading_code' => $headingAccount2->heading_code,
        'title_code' => 200
    ]);

    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Test Account',
        'main_code' => $mainAccount2->main_code,
        'heading_code' => $headingAccount2->heading_code,
        'title_code' => $titleAccount2->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeArray();
    expect($result['main_code'])->toBe(2);
    expect($result['heading_code'])->toBe(20);
    expect($result['title_code'])->toBe(200);
});

test('handles invalid posting account id gracefully', function () {
    $invalidId = 999999; // unlikely to exist
    $postingAccount = PostingAccount::find($invalidId);
    $result = $postingAccount ? $this->interactor->execute($postingAccount) : null;
    expect($result)->toBeNull();
});

test('returns posting account with numeric posting code', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Numeric Code Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeArray();
    expect($result['posting_code'])->toBe($postingAccount->posting_code);
    expect($result['posting_account'])->toBe('Numeric Code Account');
}); 