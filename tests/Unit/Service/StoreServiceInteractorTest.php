<?php

use App\Models\Service;
use App\UseCases\Service\StoreServiceInteractor;
use App\UseCases\Service\Requests\ServiceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new StoreServiceInteractor();
});

test('creates service successfully', function () {
    $serviceData = ServiceRequest::from([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => '25.00'
    ]);

    $result = $this->interactor->execute($serviceData);

    expect($result)->toBeArray();
    expect($result['code'])->toBe('SVC001');
    expect($result['description'])->toBe('Haircut Service');
    expect($result['price'])->toBe('25.00');

    $this->assertDatabaseHas('services', [
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => '25.00'
    ]);
});

test('creates service with special characters in description', function () {
    $serviceData = ServiceRequest::from([
        'code' => 'SVC001',
        'description' => 'Spa & Relaxation – "Deluxe" Service',
        'price' => '120.00'
    ]);

    $result = $this->interactor->execute($serviceData);

    expect($result)->toBeArray();
    expect($result['description'])->toBe('Spa & Relaxation – "Deluxe" Service');

    $this->assertDatabaseHas('services', [
        'code' => 'SVC001',
        'description' => 'Spa & Relaxation – "Deluxe" Service'
    ]);
});

test('creates service with max length description', function () {
    $desc = str_repeat('a', 255);
    $serviceData = ServiceRequest::from([
        'code' => 'SVC001',
        'description' => $desc,
        'price' => '50.00'
    ]);

    $result = $this->interactor->execute($serviceData);

    expect($result)->toBeArray();
    expect($result['description'])->toBe($desc);

    $this->assertDatabaseHas('services', [
        'code' => 'SVC001',
        'description' => $desc
    ]);
});

test('creates service with different price formats', function () {
    $serviceData = ServiceRequest::from([
        'code' => 'SVC001',
        'description' => 'Basic Service',
        'price' => '10'
    ]);

    $result = $this->interactor->execute($serviceData);

    expect($result)->toBeArray();
    expect($result['price'])->toBe('10');

    $this->assertDatabaseHas('services', [
        'code' => 'SVC001',
        'price' => '10'
    ]);
});

test('creates service with numeric code', function () {
    $serviceData = ServiceRequest::from([
        'code' => '001',
        'description' => 'Numeric Code Service',
        'price' => '25.00'
    ]);

    $result = $this->interactor->execute($serviceData);

    expect($result)->toBeArray();
    expect($result['code'])->toBe('001');

    $this->assertDatabaseHas('services', [
        'code' => '001',
        'description' => 'Numeric Code Service'
    ]);
});

test('creates service with alphanumeric code', function () {
    $serviceData = ServiceRequest::from([
        'code' => 'SVC-001-A',
        'description' => 'Alphanumeric Code Service',
        'price' => '75.00'
    ]);

    $result = $this->interactor->execute($serviceData);

    expect($result)->toBeArray();
    expect($result['code'])->toBe('SVC-001-A');

    $this->assertDatabaseHas('services', [
        'code' => 'SVC-001-A',
        'description' => 'Alphanumeric Code Service'
    ]);
});

test('creates multiple services', function () {
    $serviceData1 = ServiceRequest::from([
        'code' => 'SVC001',
        'description' => 'Service One',
        'price' => '25.00'
    ]);

    $serviceData2 = ServiceRequest::from([
        'code' => 'SVC002',
        'description' => 'Service Two',
        'price' => '50.00'
    ]);

    $result1 = $this->interactor->execute($serviceData1);
    $result2 = $this->interactor->execute($serviceData2);

    expect($result1)->toBeArray();
    expect($result2)->toBeArray();
    expect($result1['code'])->toBe('SVC001');
    expect($result2['code'])->toBe('SVC002');

    $this->assertDatabaseHas('services', [
        'code' => 'SVC001',
        'description' => 'Service One'
    ]);
    $this->assertDatabaseHas('services', [
        'code' => 'SVC002',
        'description' => 'Service Two'
    ]);
}); 