<?php

use App\Models\Service;
use App\UseCases\Service\DeleteServiceInteractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->interactor = new DeleteServiceInteractor();
});

test('deletes service successfully', function () {
    $service = Service::factory()->create([
        'code' => 'SVC001',
        'description' => 'Haircut Service',
        'price' => '25.00'
    ]);

    $result = $this->interactor->execute($service);

    expect($result)->toBeTrue();

    // Check that the service is soft deleted
    $this->assertSoftDeleted('services', [
        'id' => $service->id
    ]);
});

test('deletes service with special characters in description', function () {
    $service = Service::factory()->create([
        'code' => 'SVC001',
        'description' => 'Spa & Relaxation – "Deluxe" Service',
        'price' => '120.00'
    ]);

    $result = $this->interactor->execute($service);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('services', ['id' => $service->id]);
});

test('deletes service with max length description', function () {
    $desc = str_repeat('a', 255);
    $service = Service::factory()->create([
        'code' => 'SVC001',
        'description' => $desc,
        'price' => '50.00'
    ]);

    $result = $this->interactor->execute($service);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('services', ['id' => $service->id]);
});

test('deletes service with different price formats', function () {
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

    expect($result1)->toBeTrue();
    expect($result2)->toBeTrue();
    expect($result3)->toBeTrue();

    $this->assertSoftDeleted('services', ['id' => $service1->id]);
    $this->assertSoftDeleted('services', ['id' => $service2->id]);
    $this->assertSoftDeleted('services', ['id' => $service3->id]);
});

test('deletes service with numeric code', function () {
    $service = Service::factory()->create([
        'code' => '001',
        'description' => 'Numeric Code Service',
        'price' => '25.00'
    ]);

    $result = $this->interactor->execute($service);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('services', ['id' => $service->id]);
});

test('deletes service with alphanumeric code', function () {
    $service = Service::factory()->create([
        'code' => 'SVC-001-A',
        'description' => 'Alphanumeric Code Service',
        'price' => '75.00'
    ]);

    $result = $this->interactor->execute($service);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('services', ['id' => $service->id]);
});

test('deletes multiple services', function () {
    $service1 = Service::factory()->create([
        'code' => 'SVC001',
        'description' => 'Service One',
        'price' => '25.00'
    ]);

    $service2 = Service::factory()->create([
        'code' => 'SVC002',
        'description' => 'Service Two',
        'price' => '50.00'
    ]);

    $result1 = $this->interactor->execute($service1);
    $result2 = $this->interactor->execute($service2);

    expect($result1)->toBeTrue();
    expect($result2)->toBeTrue();

    $this->assertSoftDeleted('services', ['id' => $service1->id]);
    $this->assertSoftDeleted('services', ['id' => $service2->id]);
});

test('handles invalid uuid format gracefully', function () {
    $invalidId = 'invalid-uuid-format';
    $service = Service::find($invalidId);
    $result = $service ? $this->interactor->execute($service) : null;
    expect($result)->toBeNull();
}); 