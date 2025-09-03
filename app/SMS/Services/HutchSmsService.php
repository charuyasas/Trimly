<?php

namespace App\SMS\Services;

use App\SMS\Contracts\SmsServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HutchSmsService implements SmsServiceInterface
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private string $defaultMask;
    private string $defaultCampaignName;

    public function __construct()
    {
        $this->baseUrl = config('services.hutch_sms.base_url');
        $this->username = config('services.hutch_sms.username');
        $this->password = config('services.hutch_sms.password');
        $this->defaultMask = config('services.hutch_sms.default_mask');
        $this->defaultCampaignName = config('services.hutch_sms.default_campaign_name');
    }

    public function send(string $to, string $message, array $options = []): array
    {
        try {
            if (!$this->isConfigured()) {
                throw new \Exception('Hutch SMS service is not properly configured');
            }

            $accessToken = $this->getAccessToken();
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'X-API-VERSION' => 'v1',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post($this->baseUrl . '/sendsms', [
                'campaignName' => $options['campaign_name'] ?? $this->defaultCampaignName,
                'mask' => $options['mask'] ?? $this->defaultMask,
                'numbers' => $to,
                'content' => $message,
                'deliveryReportRequest' => $options['delivery_report_request'] ?? true,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'SMS sent successfully'
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to send SMS',
                'status' => $response->status(),
                'response' => $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && 
               !empty($this->username) && 
               !empty($this->password) && 
               !empty($this->defaultMask);
    }

    private function getAccessToken(): string
    {
        $cachedToken = Cache::get('hutch_sms_access_token');
        
        if ($cachedToken) {
            return $cachedToken;
        }

        return $this->authenticate();
    }

    private function authenticate(): string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'X-API-VERSION' => 'v1',
        ])->post($this->baseUrl . '/login', [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to authenticate with Hutch SMS service: ' . $response->body());
        }

        $data = $response->json();
        
        if (!isset($data['accessToken'])) {
            throw new \Exception('Invalid response from Hutch SMS service: access token not found');
        }

        Cache::put('hutch_sms_access_token', $data['accessToken'], now()->addMinutes(50));
        return $data['accessToken'];
    }
}
