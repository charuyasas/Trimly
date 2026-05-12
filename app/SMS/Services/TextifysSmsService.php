<?php

namespace App\SMS\Services;

use App\SMS\Contracts\SmsServiceInterface;
use Illuminate\Support\Facades\Http;

class TextifysSmsService implements SmsServiceInterface
{
    private string $baseUrl;

    private string $apiKey;

    private string $defaultMask;

    public function __construct()
    {
        $this->baseUrl = config('services.textifys_sms.base_url');
        $this->apiKey = config('services.textifys_sms.api_key');
        $this->defaultMask = config('services.textifys_sms.default_mask');
    }

    public function send(string $to, string $message, array $options = []): array
    {
        try {
            if (! $this->isConfigured()) {
                throw new \Exception('Textifys SMS service is not properly configured');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post(rtrim($this->baseUrl, '/').'/send-sms', [
                'to' => $to,
                'message' => $message,
                'mask' => $options['mask'] ?? $this->defaultMask,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'SMS sent successfully',
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to send SMS',
                'status' => $response->status(),
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function isConfigured(): bool
    {
        return ! empty($this->baseUrl) &&
            ! empty($this->apiKey) &&
            ! empty($this->defaultMask);
    }
}
