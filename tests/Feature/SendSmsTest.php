<?php

use App\SMS\Contracts\SmsServiceInterface;
use App\SMS\Services\HutchSmsService;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::forget('hutch_sms_access_token');
});

test('sms service can be resolved from container', function () {
    $smsService = app(SmsServiceInterface::class);
    expect($smsService)->toBeInstanceOf(HutchSmsService::class);
});

test('sms service implements correct interface', function () {
    $smsService = app(SmsServiceInterface::class);
    expect($smsService)->toBeInstanceOf(SmsServiceInterface::class);
});

test('sms service is properly configured', function () {
    $smsService = app(SmsServiceInterface::class);
    expect($smsService->isConfigured())->toBeTrue();
});

test('can send sms with actual service', function () {
    $smsService = app(SmsServiceInterface::class);
    
    $result = $smsService->send('94711079500', 'Test message from Laravel - ' . now()->format('Y-m-d H:i:s'));
    
    expect($result['success'])->toBeTrue();
    expect($result['data'])->toHaveKey('serverRef');
    expect($result['message'])->toBe('SMS sent successfully');
});

test('can send sms with custom options', function () {
    $smsService = app(SmsServiceInterface::class);
    
    $result = $smsService->send('94711079500', 'Custom test message - ' . now()->format('Y-m-d H:i:s'), [
        'mask' => 'ECHODATA',
        'campaign_name' => 'Test Campaign ' . now()->format('Y-m-d')
    ]);
    
    expect($result['success'])->toBeTrue();
    expect($result['data'])->toHaveKey('serverRef');
});

test('can send sms to specific number 94711079500', function () {
    $smsService = app(SmsServiceInterface::class);
    
    $result = $smsService->send('94711079500', 'This is a test message from Laravel application using OAuth 2.0 - ' . now()->format('Y-m-d H:i:s'));
    
    expect($result['success'])->toBeTrue();
    expect($result['data'])->toHaveKey('serverRef');
    expect($result['message'])->toBe('SMS sent successfully');
});
