<?php

namespace App\SMS\Support;

use App\SMS\Contracts\SmsServiceInterface;

class SmsSender
{
    public static function send(?string $rawNumber, string $message): void
    {
        if (empty($rawNumber)) {
            return;
        }

        $to = PhoneNumberFormatter::format((string) $rawNumber);
        if ($to === '' || preg_match('/^\d{9,15}$/', $to) !== 1) {
            return;
        }

        try {
            $smsService = app(SmsServiceInterface::class);
            $smsService->send($to, $message, [
                'mask' => 'DreamBarber',
                'campaign_name' => 'Dream Barber '.now()->format('Y-m-d'),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
