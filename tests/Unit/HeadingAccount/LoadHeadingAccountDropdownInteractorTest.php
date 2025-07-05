<?php

use App\Models\HeadingAccount;
use App\UseCases\HeadingAccount\LoadHeadingAccountDropdownInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\MainAccount;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new LoadHeadingAccountDropdownInteractor();
});

test('returns all heading accounts for dropdown', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    
    $headingAccount1 = HeadingAccount::factory()->create([
        'heading_code' => 100,
        'heading_account' => 'Cash and Cash Equivalents',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount2 = HeadingAccount::factory()->create([
        'heading_code' => 200,
        'heading_account' => 'Accounts Receivable',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount3 = HeadingAccount::factory()->create([
        'heading_code' => 300,
        'heading_account' => 'Inventory',
        'main_code' => $mainAccount->main_code
    ]);

    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toHaveCount(3);
    expect($result[0])->toBeArray();
    expect($result[1])->toBeArray();
    expect($result[2])->toBeArray();
    expect($result->pluck('label')->toArray())->toContain('100 - Cash and Cash Equivalents');
    expect($result->pluck('label')->toArray())->toContain('200 - Accounts Receivable');
    expect($result->pluck('label')->toArray())->toContain('300 - Inventory');
});

test('returns empty collection when no heading accounts exist', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toBeEmpty();
    expect($result)->toHaveCount(0);
});

test('returns heading accounts ordered by heading_code', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    
    $headingAccount3 = HeadingAccount::factory()->create([
        'heading_code' => 300,
        'heading_account' => 'Inventory',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount1 = HeadingAccount::factory()->create([
        'heading_code' => 100,
        'heading_account' => 'Cash and Cash Equivalents',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount2 = HeadingAccount::factory()->create([
        'heading_code' => 200,
        'heading_account' => 'Accounts Receivable',
        'main_code' => $mainAccount->main_code
    ]);

    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toHaveCount(3);
    expect($result[0]['value'])->toBe(100);
    expect($result[1]['value'])->toBe(200);
    expect($result[2]['value'])->toBe(300);
});

test('returns heading accounts with special characters in name', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    
    $headingAccount1 = HeadingAccount::factory()->create([
        'heading_code' => 100,
        'heading_account' => 'Cash & Cash Equivalents',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount2 = HeadingAccount::factory()->create([
        'heading_code' => 200,
        'heading_account' => 'Accounts Receivable (Net)',
        'main_code' => $mainAccount->main_code
    ]);

    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toHaveCount(2);
    expect($result->pluck('label')->toArray())->toContain('100 - Cash & Cash Equivalents');
    expect($result->pluck('label')->toArray())->toContain('200 - Accounts Receivable (Net)');
});

test('returns heading accounts with max length name', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    $headingAccountName = str_repeat('a', 255);
    $headingAccount = HeadingAccount::factory()->create([
        'heading_code' => 100,
        'heading_account' => $headingAccountName,
        'main_code' => $mainAccount->main_code
    ]);

    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('100 - ' . $headingAccountName);
});

test('returns heading accounts with different code formats', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    
    $headingAccount1 = HeadingAccount::factory()->create([
        'heading_code' => 100,
        'heading_account' => 'Cash and Cash Equivalents',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount2 = HeadingAccount::factory()->create([
        'heading_code' => 1000,
        'heading_account' => 'Accounts Receivable',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount3 = HeadingAccount::factory()->create([
        'heading_code' => 10000,
        'heading_account' => 'Inventory',
        'main_code' => $mainAccount->main_code
    ]);

    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toHaveCount(3);
    expect($result->pluck('value')->toArray())->toContain(100);
    expect($result->pluck('value')->toArray())->toContain(1000);
    expect($result->pluck('value')->toArray())->toContain(10000);
});

test('returns heading accounts with international characters', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    
    $headingAccount = HeadingAccount::factory()->create([
        'heading_code' => 100,
        'heading_account' => 'Efectivo y Equivalentes (Español)',
        'main_code' => $mainAccount->main_code
    ]);

    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('100 - Efectivo y Equivalentes (Español)');
});

test('returns heading accounts with numeric names', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    
    $headingAccount = HeadingAccount::factory()->create([
        'heading_code' => 100,
        'heading_account' => '1000 - Cash and Cash Equivalents',
        'main_code' => $mainAccount->main_code
    ]);

    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('100 - 1000 - Cash and Cash Equivalents');
});

test('returns heading accounts with alphanumeric codes', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    
    $headingAccount1 = HeadingAccount::factory()->create([
        'heading_code' => 100,
        'heading_account' => 'Cash and Cash Equivalents',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount2 = HeadingAccount::factory()->create([
        'heading_code' => 200,
        'heading_account' => 'Accounts Receivable',
        'main_code' => $mainAccount->main_code
    ]);

    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toHaveCount(2);
    expect($result[0]['value'])->toBe(100);
    expect($result[1]['value'])->toBe(200);
    expect($result[0]['label'])->toBe('100 - Cash and Cash Equivalents');
    expect($result[1]['label'])->toBe('200 - Accounts Receivable');
});

test('returns heading accounts with complex naming patterns', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    
    $headingAccount1 = HeadingAccount::factory()->create([
        'heading_code' => 100,
        'heading_account' => 'Cash & Cash Equivalents',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount2 = HeadingAccount::factory()->create([
        'heading_code' => 200,
        'heading_account' => 'Accounts Receivable (Net of Allowance)',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount3 = HeadingAccount::factory()->create([
        'heading_code' => 300,
        'heading_account' => 'Inventory - Raw Materials',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount4 = HeadingAccount::factory()->create([
        'heading_code' => 400,
        'heading_account' => 'Prepaid Expenses & Deposits',
        'main_code' => $mainAccount->main_code
    ]);

    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toHaveCount(4);
    expect($result->pluck('label')->toArray())->toContain('100 - Cash & Cash Equivalents');
    expect($result->pluck('label')->toArray())->toContain('200 - Accounts Receivable (Net of Allowance)');
    expect($result->pluck('label')->toArray())->toContain('300 - Inventory - Raw Materials');
    expect($result->pluck('label')->toArray())->toContain('400 - Prepaid Expenses & Deposits');
});

test('returns heading accounts with financial terminology', function () {
    $mainAccount = MainAccount::factory()->create(['main_code' => 1000]);
    
    $headingAccount1 = HeadingAccount::factory()->create([
        'heading_code' => 100,
        'heading_account' => 'Bank Deposits',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount2 = HeadingAccount::factory()->create([
        'heading_code' => 200,
        'heading_account' => 'Trade Receivables',
        'main_code' => $mainAccount->main_code
    ]);

    $headingAccount3 = HeadingAccount::factory()->create([
        'heading_code' => 300,
        'heading_account' => 'Finished Goods Inventory',
        'main_code' => $mainAccount->main_code
    ]);

    $result = $this->interactor->execute('', $mainAccount->main_code);

    expect($result)->toHaveCount(3);
    expect($result->pluck('label')->toArray())->toContain('100 - Bank Deposits');
    expect($result->pluck('label')->toArray())->toContain('200 - Trade Receivables');
    expect($result->pluck('label')->toArray())->toContain('300 - Finished Goods Inventory');
}); 