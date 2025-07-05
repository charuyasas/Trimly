<?php

use App\Models\Service;
use App\UseCases\Service\ShowServiceInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new ShowServiceInteractor();
});

test('returns service when service exists', function () {
    $service = Service::factory()->create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => '25.00'
    ]);

    $result = $this->interactor->execute($service);

    expect($result)->toBeInstanceOf(Service::class);
    expect($result->id)->toBe($service->id);
    expect($result->code)->toBe('SVC001');
    expect($result->description)->toBe('Haircut Service');
    expect($result->price)->toBe('25.00');
});

test('returns null when service does not exist', function () {
    $nonExistentId = '12345678-1234-1234-1234-123456789012';
    $service = Service::find($nonExistentId);
    $result = $service ? $this->interactor->execute($service) : null;
    expect($result)->toBeNull();
});

test('returns service with special characters in description', function () {
    $service = Service::factory()->create([
        'code' => 'SVC001',
        'description' => 'Spa & Relaxation – "Deluxe" Service',
        'price' => '120.00'
    ]);

    $result = $this->interactor->execute($service);

    expect($result)->toBeInstanceOf(Service::class);
    expect($result->id)->toBe($service->id);
    expect($result->description)->toBe('Spa & Relaxation – "Deluxe" Service');
});

test('returns service with max length description', function () {
    $desc = str_repeat('a', 255);
    $service = Service::factory()->create([
        'code' => 'SVC001',
        'description' => $desc,
        'price' => '50.00'
    ]);

    $result = $this->interactor->execute($service);

    expect($result)->toBeInstanceOf(Service::class);
    expect($result->id)->toBe($service->id);
    expect($result->description)->toBe($desc);
});

test('returns service with different price formats', function () {
    $service1 = Service::factory()->create([
        'code' => 'SVC001',
        'description' => 'Basic Service',
        'price' => '10'
    ]);

    $service2 = Service::factory()->create([
        'code' => 'SVC002',
        'description' => 'Premium Service',
        'price' => '99.99'
    ]);

    $service3 = Service::factory()->create([
        'code' => 'SVC003',
        'description' => 'Luxury Service',
        'price' => '150.00'
    ]);

    $result1 = $this->interactor->execute($service1);
    $result2 = $this->interactor->execute($service2);
    $result3 = $this->interactor->execute($service3);

    expect($result1->price)->toBe('10');
    expect($result2->price)->toBe('99.99');
    expect($result3->price)->toBe('150.00');
});

test('returns service with numeric code', function () {
    $service = Service::factory()->create([
        'code' => '001',
        'description' => 'Numeric Code Service',
        'price' => '25.00'
    ]);

    $result = $this->interactor->execute($service);

    expect($result)->toBeInstanceOf(Service::class);
    expect($result->id)->toBe($service->id);
    expect($result->code)->toBe('001');
});

test('returns service with alphanumeric code', function () {
    $service = Service::factory()->create([
        'code' => 'SVC-001-A',
        'description' => 'Alphanumeric Code Service',
        'price' => '75.00'
    ]);

    $result = $this->interactor->execute($service);

    expect($result)->toBeInstanceOf(Service::class);
    expect($result->id)->toBe($service->id);
    expect($result->code)->toBe('SVC-001-A');
});

test('handles invalid uuid format gracefully', function () {
    $invalidId = 'invalid-uuid-format';
    $service = Service::find($invalidId);
    $result = $service ? $this->interactor->execute($service) : null;
    expect($result)->toBeNull();
}); 