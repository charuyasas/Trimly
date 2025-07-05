<?php

use App\Models\PostingAccount;
use App\Models\MainAccount;
use App\Models\HeadingAccount;
use App\Models\TitleAccount;
use App\UseCases\PostingAccount\StorePostingAccountInteractor;
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;
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

    $this->interactor = new StorePostingAccountInteractor();
});

test('creates a new posting account successfully', function () {
    $postingAccountData = PostingAccountRequest::from([
        'posting_account' => 'Cash in Bank',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccountData);

    expect($result)->toBeArray();
    expect($result['posting_account'])->toBe('Cash in Bank');
    expect($result['main_code'])->toBe($this->mainAccount->main_code);
    expect($result['heading_code'])->toBe($this->headingAccount->heading_code);
    expect($result['title_code'])->toBe($this->titleAccount->title_code);

    $this->assertDatabaseHas('posting_accounts', [
        'posting_account' => 'Cash in Bank',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);
});

test('creates posting account with special characters', function () {
    $postingAccountName = 'Cash & Bank – "Primary" Account';
    $postingAccountData = PostingAccountRequest::from([
        'posting_account' => $postingAccountName,
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccountData);

    expect($result)->toBeArray();
    expect($result['posting_account'])->toBe($postingAccountName);
    $this->assertDatabaseHas('posting_accounts', [
        'posting_account' => $postingAccountName
    ]);
});

test('creates posting account with max length name', function () {
    $postingAccountName = str_repeat('a', 255);
    $postingAccountData = PostingAccountRequest::from([
        'posting_account' => $postingAccountName,
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result = $this->interactor->execute($postingAccountData);

    expect($result)->toBeArray();
    expect($result['posting_account'])->toBe($postingAccountName);
    $this->assertDatabaseHas('posting_accounts', [
        'posting_account' => $postingAccountName
    ]);
});

test('creates multiple posting accounts', function () {
    $postingAccount1 = PostingAccountRequest::from([
        'posting_account' => 'Account One',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $postingAccount2 = PostingAccountRequest::from([
        'posting_account' => 'Account Two',
        'main_code' => $this->mainAccount->main_code,
        'heading_code' => $this->headingAccount->heading_code,
        'title_code' => $this->titleAccount->title_code
    ]);

    $result1 = $this->interactor->execute($postingAccount1);
    $result2 = $this->interactor->execute($postingAccount2);

    expect($result1)->toBeArray();
    expect($result2)->toBeArray();
    expect($result1['posting_code'])->not->toBe($result2['posting_code']);

    $this->assertDatabaseHas('posting_accounts', [
        'posting_account' => 'Account One'
    ]);
    $this->assertDatabaseHas('posting_accounts', [
        'posting_account' => 'Account Two'
    ]);
});

test('fails validation when posting account name is empty', function () {
    $dto = PostingAccountRequest::from([
        'posting_account' => '',
        'main_code' => 1,
        'heading_code' => 10,
        'title_code' => 100
    ]);
    expect($dto)->toBeInstanceOf(PostingAccountRequest::class);
});

test('fails validation when posting account name exceeds max length', function () {
    $dto = PostingAccountRequest::from([
        'posting_account' => str_repeat('a', 256),
        'main_code' => 1,
        'heading_code' => 10,
        'title_code' => 100
    ]);
    expect($dto)->toBeInstanceOf(PostingAccountRequest::class);
});

test('fails validation when main_code is invalid', function () {
    $dto = PostingAccountRequest::from([
        'posting_account' => 'Valid Name',
        'main_code' => 999999, // assuming this does not exist
        'heading_code' => 10,
        'title_code' => 100
    ]);
    expect($dto)->toBeInstanceOf(PostingAccountRequest::class);
});

test('fails validation when heading_code is invalid', function () {
    $dto = PostingAccountRequest::from([
        'posting_account' => 'Valid Name',
        'main_code' => 1,
        'heading_code' => 999999, // assuming this does not exist
        'title_code' => 100
    ]);
    expect($dto)->toBeInstanceOf(PostingAccountRequest::class);
});

test('fails validation when title_code is invalid', function () {
    $dto = PostingAccountRequest::from([
        'posting_account' => 'Valid Name',
        'main_code' => 1,
        'heading_code' => 10,
        'title_code' => 999999 // assuming this does not exist
    ]);
    expect($dto)->toBeInstanceOf(PostingAccountRequest::class);
});

test('fails validation when main_code is not integer', function () {
    expect(function () {
        PostingAccountRequest::from([
            'posting_account' => 'Valid Name',
            'main_code' => 'not-an-integer',
            'heading_code' => 10,
            'title_code' => 100
        ]);
    })->toThrow(TypeError::class);
});

test('fails validation when heading_code is not integer', function () {
    expect(function () {
        PostingAccountRequest::from([
            'posting_account' => 'Valid Name',
            'main_code' => 1,
            'heading_code' => 'not-an-integer',
            'title_code' => 100
        ]);
    })->toThrow(TypeError::class);
});

test('fails validation when title_code is not integer', function () {
    expect(function () {
        PostingAccountRequest::from([
            'posting_account' => 'Valid Name',
            'main_code' => 1,
            'heading_code' => 10,
            'title_code' => 'not-an-integer'
        ]);
    })->toThrow(TypeError::class);
});
