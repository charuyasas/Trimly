<?php

namespace App\SMS\Contracts;

interface SmsServiceInterface
{
    /**
     * Send an SMS message
     */
    public function send(string $to, string $message, array $options = []): array;

    /**
     * Check if the service is properly configured
     */
    public function isConfigured(): bool;
}
