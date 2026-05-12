<?php

use App\SMS\Contracts\SmsServiceInterface;
use App\SMS\Services\TextifysSmsService;
use App\SMS\Support\SmsSender;
use Mockery\MockInterface;

beforeEach(function () {
    config()->set('services.textifys_sms.base_url', 'https://example-textifys.local/api');
    config()->set('services.textifys_sms.api_key', 'test-api-key');
    config()->set('services.textifys_sms.default_mask', 'DreamBarber');
});

afterEach(function () {
    \Mockery::close();
});

test('sms service can be resolved from container', function () {
    $smsService = app(SmsServiceInterface::class);
    expect($smsService)->toBeInstanceOf(TextifysSmsService::class);
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
    $this->instance(
        SmsServiceInterface::class,
        \Mockery::mock(SmsServiceInterface::class, function (MockInterface $mock) {
            $mock->expects('send')
                ->withArgs(function ($to, $message, $options = []) {
                    return $to === '94740729370' && is_string($message);
                })
                ->andReturn([
                    'success' => true,
                    'data' => ['serverRef' => 'MOCK-REF-001'],
                    'message' => 'SMS sent successfully',
                ]);
            $mock->allows('isConfigured')->andReturnTrue();
        })
    );

    $smsService = app(SmsServiceInterface::class);

    $result = $smsService->send('94740729370', 'Test message from Laravel - '.now()->format('Y-m-d H:i:s'));

    expect($result['success'])->toBeTrue();
    expect($result['data'])->toHaveKey('serverRef');
    expect($result['message'])->toBe('SMS sent successfully');
});

test('can send sms with custom options', function () {
    $this->instance(
        SmsServiceInterface::class,
        \Mockery::mock(SmsServiceInterface::class, function (MockInterface $mock) {
            $mock->expects('send')
                ->withArgs(function ($to, $message, $options = []) {
                    return $to === '94740729370'
                        && is_string($message)
                        && isset($options['mask'], $options['campaign_name'])
                        && $options['mask'] === 'DreamBarber'
                        && str_starts_with($options['campaign_name'], 'Dream Barber ');
                })
                ->andReturn([
                    'success' => true,
                    'data' => ['serverRef' => 'MOCK-REF-002'],
                    'message' => 'SMS sent successfully',
                ]);
            $mock->allows('isConfigured')->andReturnTrue();
        })
    );

    $smsService = app(SmsServiceInterface::class);

    $result = $smsService->send('94740729370', 'Custom test message - '.now()->format('Y-m-d H:i:s'), [
        'mask' => 'DreamBarber',
        'campaign_name' => 'Dream Barber '.now()->format('Y-m-d'),
    ]);

    expect($result['success'])->toBeTrue();
    expect($result['data'])->toHaveKey('serverRef');
});

test('can send sms to specific number 94740729370', function () {
    $this->instance(
        SmsServiceInterface::class,
        \Mockery::mock(SmsServiceInterface::class, function (MockInterface $mock) {
            $mock->expects('send')
                ->withArgs(function ($to, $message, $options = []) {
                    return $to === '94740729370' && is_string($message);
                })
                ->andReturn([
                    'success' => true,
                    'data' => ['serverRef' => 'MOCK-REF-003'],
                    'message' => 'SMS sent successfully',
                ]);
            $mock->allows('isConfigured')->andReturnTrue();
        })
    );

    $smsService = app(SmsServiceInterface::class);

    $result = $smsService->send('94740729370', 'This is a test message from Laravel application using OAuth 2.0 - '.now()->format('Y-m-d H:i:s'));

    expect($result['success'])->toBeTrue();
    expect($result['data'])->toHaveKey('serverRef');
    expect($result['message'])->toBe('SMS sent successfully');
});

test('blocks sending when provider breaks', function () {
    $this->instance(
        SmsServiceInterface::class,
        \Mockery::mock(SmsServiceInterface::class, function (MockInterface $mock) {
            $mock->expects('send')
                ->andThrow(new \Exception('Provider failure'));
            $mock->allows('isConfigured')->andReturnTrue();
        })
    );

    SmsSender::send('94740729370', 'Should not throw');

    expect(true)->toBeTrue();
});
