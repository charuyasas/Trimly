<?php

use App\Models\Service;
use App\UseCases\Invoice\LoadItemDropdownInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new LoadItemDropdownInteractor();
});

test('returns services matching code search', function () {
    Service::create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    Service::create([
        'code' => 'SVC002',
        'description' => 'Hair Coloring',
        'price' => 50.00
    ]);

    Service::create([
        'code' => 'PROD001',
        'description' => 'Hair Product',
        'price' => 15.00
    ]);

    $result = $this->interactor->execute('SVC');

    expect($result)->toHaveCount(2);
    expect($result[0]['label'])->toBe('SVC001 - Haircut Service');
    expect($result[0]['value'])->toBe(Service::where('code', 'SVC001')->first()->id);
    expect($result[0]['price'])->toBe('25.00');

    expect($result[1]['label'])->toBe('SVC002 - Hair Coloring');
    expect($result[1]['value'])->toBe(Service::where('code', 'SVC002')->first()->id);
    expect($result[1]['price'])->toBe('50.00');
});

test('returns services matching description search', function () {
    Service::create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    Service::create([
        'code' => 'SVC002',
        'description' => 'Hair Coloring',
        'price' => 50.00
    ]);

    Service::create([
        'code' => 'PROD001',
        'description' => 'Hair Product',
        'price' => 15.00
    ]);

    $result = $this->interactor->execute('Haircut');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('SVC001 - Haircut Service');
    expect($result[0]['value'])->toBe(Service::where('code', 'SVC001')->first()->id);
    expect($result[0]['price'])->toBe('25.00');
});

test('returns all services when search is empty', function () {
    Service::create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    Service::create([
        'code' => 'SVC002',
        'description' => 'Hair Coloring',
        'price' => 50.00
    ]);

    Service::create([
        'code' => 'PROD001',
        'description' => 'Hair Product',
        'price' => 15.00
    ]);

    $result = $this->interactor->execute('');

    expect($result)->toHaveCount(3);
    expect($result[0]['label'])->toBe('PROD001 - Hair Product'); // Ordered by code
    expect($result[1]['label'])->toBe('SVC001 - Haircut Service');
    expect($result[2]['label'])->toBe('SVC002 - Hair Coloring');
});

test('returns all services when search is null', function () {
    Service::create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    Service::create([
        'code' => 'SVC002',
        'description' => 'Hair Coloring',
        'price' => 50.00
    ]);

    $result = $this->interactor->execute(null);

    expect($result)->toHaveCount(2);
    expect($result[0]['label'])->toBe('SVC001 - Haircut Service');
    expect($result[1]['label'])->toBe('SVC002 - Hair Coloring');
});

test('limits results to 10 items', function () {
    // Create 15 services
    for ($i = 1; $i <= 15; $i++) {
        Service::create([
            'code' => 'SVC' . str_pad($i, 3, '0', STR_PAD_LEFT),
            'description' => "Service $i",
            'price' => 25.00
        ]);
    }

    $result = $this->interactor->execute('');

    expect($result)->toHaveCount(10);
    expect($result[0]['label'])->toBe('SVC001 - Service 1');
    expect($result[9]['label'])->toBe('SVC010 - Service 10');
});

test('orders results by code ascending', function () {
    Service::create([
        'code' => 'SVC003',
        'description' => 'Service 3',
        'price' => 25.00
    ]);

    Service::create([
        'code' => 'SVC001',
        'description' => 'Service 1',
        'price' => 25.00
    ]);

    Service::create([
        'code' => 'SVC002',
        'description' => 'Service 2',
        'price' => 25.00
    ]);

    $result = $this->interactor->execute('');

    expect($result)->toHaveCount(3);
    expect($result[0]['label'])->toBe('SVC001 - Service 1');
    expect($result[1]['label'])->toBe('SVC002 - Service 2');
    expect($result[2]['label'])->toBe('SVC003 - Service 3');
});

test('handles case insensitive search', function () {
    Service::create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    Service::create([
        'code' => 'SVC002',
        'description' => 'Hair Coloring',
        'price' => 50.00
    ]);

    $result = $this->interactor->execute('haircut');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('SVC001 - Haircut Service');
});

test('handles partial matches', function () {
    Service::create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    Service::create([
        'code' => 'SVC002',
        'description' => 'Hair Coloring',
        'price' => 50.00
    ]);

    Service::create([
        'code' => 'PROD001',
        'description' => 'Hair Product',
        'price' => 15.00
    ]);

    $result = $this->interactor->execute('Hair');

    expect($result)->toHaveCount(3);
    expect($result[0]['label'])->toBe('PROD001 - Hair Product');
    expect($result[1]['label'])->toBe('SVC001 - Haircut Service');
    expect($result[2]['label'])->toBe('SVC002 - Hair Coloring');
});

test('returns empty array when no matches found', function () {
    Service::create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    $result = $this->interactor->execute('NonExistent');

    expect($result)->toBeEmpty();
});

test('handles special characters in search', function () {
    Service::create([
        'code' => 'SVC-001',
        'description' => 'Haircut & Styling',
        'price' => 25.00
    ]);

    Service::create([
        'code' => 'SVC_002',
        'description' => 'Hair Coloring (Premium)',
        'price' => 50.00
    ]);

    $result = $this->interactor->execute('Haircut &');

    expect($result)->toHaveCount(1);
    expect($result[0]['label'])->toBe('SVC-001 - Haircut & Styling');
});

test('includes correct structure for each result', function () {
    Service::create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    $result = $this->interactor->execute('SVC001');

    expect($result)->toHaveCount(1);
    expect($result[0])->toHaveKeys(['label', 'value', 'price']);
    expect($result[0]['label'])->toBe('SVC001 - Haircut Service');
    expect($result[0]['value'])->toBe(Service::where('code', 'SVC001')->first()->id);
    expect($result[0]['price'])->toBe('25.00');
});

test('handles soft deleted services', function () {
    $service = Service::create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => 25.00
    ]);

    // Soft delete the service
    $service->delete();

    $result = $this->interactor->execute('SVC001');

    expect($result)->toBeEmpty();
});

test('handles zero price services', function () {
    Service::create([
        'code' => 'SVC001',
        'description' => 'Free Service',
        'price' => 0.00
    ]);

    $result = $this->interactor->execute('SVC001');

    expect($result)->toHaveCount(1);
    expect($result[0]['price'])->toBe('0.00');
});

test('handles large price values', function () {
    Service::create([
        'code' => 'SVC001',
        'description' => 'Premium Service',
        'price' => 999.99
    ]);

    $result = $this->interactor->execute('SVC001');

    expect($result)->toHaveCount(1);
    expect($result[0]['price'])->toBe('999.99');
}); 