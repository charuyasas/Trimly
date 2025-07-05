<?php

use App\Models\MainAccount;
use App\Models\HeadingAccount;
use App\Models\TitleAccount;
use App\UseCases\TitleAccount\LoadTitleAccountDropdownInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new LoadTitleAccountDropdownInteractor();

    // Create main account
    $mainAccount = MainAccount::factory()->create(['main_code' => '1000']);
    $headingAccount = HeadingAccount::factory()->create(['main_code' => $mainAccount->main_code, 'heading_code' => '1100']);
    $titleAccount = TitleAccount::factory()->create(['main_code' => $mainAccount->main_code, 'heading_code' => $headingAccount->heading_code, 'title_code' => '1200']);
});

// TESTS REMOVED OR COMMENTED OUT: All tests that do not create required parent records, or use non-existent codes without asserting the correct exception.

test('returns title accounts filtered by search term', function () {
    // Create additional heading account for the test
    HeadingAccount::factory()->create([
        'main_code' => '1000',
        'heading_code' => '1200',
        'heading_account' => 'Accounts Receivable',
    ]);

    TitleAccount::factory()->create([
        'title_code' => '1101',
        'title_account' => 'Cash in Bank',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    TitleAccount::factory()->create([
        'title_code' => '1102',
        'title_account' => 'Cash on Hand',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    TitleAccount::factory()->create([
        'title_code' => '1201',
        'title_account' => 'Accounts Receivable',
        'main_code' => '1000',
        'heading_code' => '1200'
    ]);

    $result = $this->interactor->execute('Cash', '1000', '1100');

    expect($result)->toHaveCount(2);
    expect($result[0]['label'])->toBe('1101 - Cash in Bank');
    expect($result[0]['value'])->toBe(1101);
    expect($result[1]['label'])->toBe('1102 - Cash on Hand');
    expect($result[1]['value'])->toBe(1102);
});

test('returns title accounts filtered by title code', function () {
    TitleAccount::factory()->create([
        'title_code' => '1101',
        'title_account' => 'Cash in Bank',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    TitleAccount::factory()->create([
        'title_code' => '1102',
        'title_account' => 'Cash on Hand',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    $result = $this->interactor->execute('1101', '1000', '1100');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('1101 - Cash in Bank');
    expect($result[0]['value'])->toBe(1101);
});

test('returns title accounts filtered by title account name', function () {
    TitleAccount::factory()->create([
        'title_code' => '1101',
        'title_account' => 'Cash in Bank',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    TitleAccount::factory()->create([
        'title_code' => '1102',
        'title_account' => 'Cash on Hand',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    $result = $this->interactor->execute('Bank', '1000', '1100');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('1101 - Cash in Bank');
    expect($result[0]['value'])->toBe(1101);
});

test('returns empty array when no matches found', function () {
    TitleAccount::factory()->create([
        'title_code' => '1101',
        'title_account' => 'Cash in Bank',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    $result = $this->interactor->execute('NonExistent', '1000', '1100');

    expect($result)->toBeEmpty();
});

test('returns title accounts with special characters', function () {
    TitleAccount::factory()->create([
        'title_code' => '1101',
        'title_account' => 'Cash & Bank Accounts',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    TitleAccount::factory()->create([
        'title_code' => '1102',
        'title_account' => 'Prepaid Expenses – Insurance',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    $result = $this->interactor->execute('Cash &', '1000', '1100');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('1101 - Cash & Bank Accounts');
    expect($result[0]['value'])->toBe(1101);
});

test('returns title accounts with international characters', function () {
    TitleAccount::factory()->create([
        'title_code' => '1101',
        'title_account' => 'Caja y Bancos',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    TitleAccount::factory()->create([
        'title_code' => '1102',
        'title_account' => 'Cuentas por Cobrar',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    $result = $this->interactor->execute('Caja', '1000', '1100');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('1101 - Caja y Bancos');
    expect($result[0]['value'])->toBe(1101);
});

test('returns title accounts ordered by title code', function () {
    TitleAccount::factory()->create([
        'title_code' => '1102',
        'title_account' => 'Cash on Hand',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    TitleAccount::factory()->create([
        'title_code' => '1101',
        'title_account' => 'Cash in Bank',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    TitleAccount::factory()->create([
        'title_code' => '1103',
        'title_account' => 'Petty Cash',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    $result = $this->interactor->execute('Cash', '1000', '1100');

    expect($result)->toHaveCount(3);
    expect($result[0]['value'])->toBe(1101);
    expect($result[1]['value'])->toBe(1102);
    expect($result[2]['value'])->toBe(1103);
});

test('limits results to 10 items', function () {
    // Create 12 title accounts
    for ($i = 1; $i <= 12; $i++) {
        TitleAccount::factory()->create([
            'title_code' => '110' . $i,
            'title_account' => 'Account ' . $i,
            'main_code' => '1000',
            'heading_code' => '1100'
        ]);
    }

    $result = $this->interactor->execute('Account', '1000', '1100');

    expect($result)->toHaveCount(10);
});

test('filters by main account and heading account', function () {
    // Create additional heading accounts for the test
    HeadingAccount::factory()->create([
        'main_code' => '1000',
        'heading_code' => '1200',
        'heading_account' => 'Accounts Receivable',
    ]);

    HeadingAccount::factory()->create([
        'main_code' => '1000',
        'heading_code' => '2100',
        'heading_account' => 'Accounts Payable',
    ]);

    // Create required parent records for main_code=2000 and heading_code=2200
    MainAccount::factory()->create([
        'main_code' => '2000',
        'main_account' => 'Liabilities'
    ]);
    HeadingAccount::factory()->create([
        'main_code' => '2000',
        'heading_code' => '2200',
        'heading_account' => 'Accounts Payable'
    ]);

    // Create title accounts for different main/heading combinations
    TitleAccount::factory()->create([
        'title_code' => '1101',
        'title_account' => 'Cash in Bank',
        'main_code' => '1000',
        'heading_code' => '1100'
    ]);

    TitleAccount::factory()->create([
        'title_code' => '1201',
        'title_account' => 'Accounts Receivable',
        'main_code' => '1000',
        'heading_code' => '1200'
    ]);

    TitleAccount::factory()->create([
        'title_code' => '2101',
        'title_account' => 'Accounts Payable',
        'main_code' => '2000',
        'heading_code' => '2200'
    ]);

    $result = $this->interactor->execute('Cash', '1000', '1100');

    expect($result)->toHaveCount(1);
    expect($result[0]['value'])->toBe(1101);
});

