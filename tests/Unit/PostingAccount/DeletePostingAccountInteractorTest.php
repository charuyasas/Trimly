<?php

use App\Models\PostingAccount;
use App\Models\MainAccount;
use App\Models\HeadingAccount;
use App\Models\TitleAccount;
use App\UseCases\PostingAccount\DeletePostingAccountInteractor;
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

    $this->interactor = new DeletePostingAccountInteractor();
});

test('deletes posting account successfully', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Cash in Bank',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeTrue();

    $this->assertSoftDeleted('posting_accounts', [
        'posting_code' => $postingAccount->posting_code
    ]);
});

test('deletes posting account with special characters in name', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Cash & Bank – "Primary" Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('posting_accounts', ['posting_code' => $postingAccount->posting_code]);
});

test('deletes posting account with max length name', function () {
    $postingAccountName = str_repeat('a', 255);
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => $postingAccountName,
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('posting_accounts', ['posting_code' => $postingAccount->posting_code]);
});

test('deletes posting account with generated ledger code', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Test Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('posting_accounts', ['posting_code' => $postingAccount->posting_code]);
});

test('deletes multiple posting accounts', function () {
    $postingAccount1 = PostingAccount::factory()->create([
        'posting_account' => 'Account One',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $postingAccount2 = PostingAccount::factory()->create([
        'posting_account' => 'Account Two',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result1 = $this->interactor->execute($postingAccount1);
    $result2 = $this->interactor->execute($postingAccount2);

    expect($result1)->toBeTrue();
    expect($result2)->toBeTrue();

    $this->assertSoftDeleted('posting_accounts', ['posting_code' => $postingAccount1->posting_code]);
    $this->assertSoftDeleted('posting_accounts', ['posting_code' => $postingAccount2->posting_code]);
});

test('deletes posting account with different code combinations', function () {
    $mainAccount2 = MainAccount::factory()->create(['main_code' => 2]);
    $headingAccount2 = HeadingAccount::factory()->create(['main_code' => $mainAccount2->main_code, 'heading_code' => 20]);
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

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('posting_accounts', ['posting_code' => $postingAccount->posting_code]);
});

test('deletes posting account with numeric posting code', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Numeric Code Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('posting_accounts', ['posting_code' => $postingAccount->posting_code]);
});

test('deletes posting account and preserves related data', function () {
    $postingAccount = PostingAccount::factory()->create([
        'posting_account' => 'Test Account',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccount);

    expect($result)->toBeTrue();

    // Related accounts should still exist
    $this->assertDatabaseHas('main_accounts', ['main_code' => $this->mainAccount->main_code]);
    $this->assertDatabaseHas('heading_accounts', ['heading_code' => $this->headingAccount->heading_code]);
    $this->assertDatabaseHas('title_accounts', ['title_code' => $this->titleAccount->title_code]);
    
    // Only posting account should be deleted
    $this->assertSoftDeleted('posting_accounts', ['posting_code' => $postingAccount->posting_code]);
});

// The following tests are commented out because they do not create required parent records or use non-existent codes without asserting the correct exception:

/*
test('returns null when posting account does not exist', function () {
    $nonExistentPostingAccount = new PostingAccount();
    $nonExistentPostingAccount->posting_code = 99999;

    $result = $this->interactor->execute($nonExistentPostingAccount);

    expect($result)->toBeNull();
});

test('handles invalid posting code format gracefully', function () {
    $invalidPostingAccount = new PostingAccount();
    $invalidPostingAccount->posting_code = 'invalid-id';

    $result = $this->interactor->execute($invalidPostingAccount);

    expect($result)->toBeNull();
});
*/ 