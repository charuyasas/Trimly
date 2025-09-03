<?php

use PHPUnit\Framework\Assert;
use Sureshhemal\SmsSriLanka\Providers\Hutch\HutchSmsService;

test('can send sms', function () {
    $smsService = app(HutchSmsService::class);

    $numbers = ['94777474646'];

    foreach ($numbers as $number) {
        $response = $smsService->sendSms(
            $number,
            'Sample msg ' . now()->format('Y-m-d H:i:s'),
            [
                'campaignName'          => 'Test',
                'deliveryReportRequest' => true,
            ]
        );

        // Dump the response for debugging
        dump($response);

        Assert::assertIsArray($response);

        // Adjust this assertion based on what you see in the dump!
        if (array_key_exists('serverRef', $response)) {
            Assert::assertArrayHasKey('serverRef', $response);
        } elseif (isset($response['result']['serverRef'])) {
            Assert::assertArrayHasKey('serverRef', $response['result']);
        } else {
            // Fail test with custom message if serverRef missing
            Assert::fail('Response does not contain serverRef key. Response dump above.');
        }
    }
});
