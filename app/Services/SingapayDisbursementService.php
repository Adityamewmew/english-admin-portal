<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SingapayDisbursementService
{
    protected string $clientId;

    protected string $clientSecret;

    protected string $partnerId;

    protected string $accountId;

    protected string $environment;

    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.singapay.client_id') ?? env('SINGAPAY_CLIENT_ID', '');
        $this->clientSecret = config('services.singapay.client_secret') ?? env('SINGAPAY_CLIENT_SECRET', '');
        $this->partnerId = config('services.singapay.partner_id') ?? env('SINGAPAY_PARTNER_ID', '');
        $this->accountId = config('services.singapay.account_id') ?? env('SINGAPAY_ACCOUNT_ID', '');
        $this->environment = config('services.singapay.environment') ?? env('SINGAPAY_ENVIRONMENT', 'sandbox');

        $this->baseUrl = strtolower($this->environment) === 'sandbox'
            ? 'https://sandbox-payment-b2b.singapay.id'
            : 'https://payment-b2b.singapay.id';
    }

    /**
     * Get OAuth Bearer Token (v1.1 with HMAC-SHA512 date signature)
     */
    public function getAccessToken(): ?string
    {
        try {
            $currentDate = date('Ymd');
            $payload = "{$this->clientId}_{$this->clientSecret}_{$currentDate}";
            $signature = hash_hmac('sha512', $payload, $this->clientSecret);

            $response = Http::withHeaders([
                'X-PARTNER-ID' => $this->partnerId,
                'X-CLIENT-ID' => $this->clientId,
                'X-Signature' => $signature,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/api/v1.1/access-token/b2b", [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $json = $response->json();

                return $json['data']['access_token'] ?? $json['access_token'] ?? null;
            }

            // Fallback to basic auth v1.0 if v1.1 is unavailable
            $basicAuth = base64_encode("{$this->clientId}:{$this->clientSecret}");
            $fallback = Http::withHeaders([
                'Authorization' => "Basic {$basicAuth}",
                'X-PARTNER-ID' => $this->partnerId,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/api/v1.0/access-token/b2b", [
                'grant_type' => 'client_credentials',
            ]);

            if ($fallback->successful()) {
                $json = $fallback->json();

                return $json['data']['access_token'] ?? $json['access_token'] ?? null;
            }

            Log::error('SingaPay token request failed', ['res' => $response->body()]);

            return null;
        } catch (Exception $e) {
            Log::error('SingapayDisbursementService::getAccessToken exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Check fee for a disbursement before transferring
     */
    public function checkFee(string $bankSwiftCode, float $amount): array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return ['success' => false, 'message' => 'Gagal mendapatkan akses token SingaPay'];
        }

        try {
            $endpoint = "/api/v1.0/disbursement/{$this->accountId}/check-fee";
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'X-PARTNER-ID' => $this->partnerId,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}{$endpoint}", [
                'bank_swift_code' => $bankSwiftCode,
                'amount' => $amount,
            ]);

            $json = $response->json();
            if ($response->successful() && ($json['success'] ?? false)) {
                return [
                    'success' => true,
                    'data' => $json['data'],
                ];
            }

            return [
                'success' => false,
                'message' => $json['error']['message'] ?? $json['message'] ?? 'Gagal menghitung biaya transfer',
            ];
        } catch (Exception $e) {
            Log::error('SingapayDisbursementService::checkFee exception: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Submit disbursement transfer to bank account
     */
    public function disburse(array $params): array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return ['success' => false, 'message' => 'Gagal mendapatkan akses token SingaPay'];
        }

        $endpoint = '/api/v2.0/disbursement/transfer';
        $body = [
            'account_id' => $this->accountId,
            'account_number' => (string) $params['account_number'],
            'amount' => (int) $params['amount'],
            'bank_code' => (string) $params['bank_code'],
            'beneficiary_name' => (string) $params['account_name'],
            'merchant_reff_no' => (string) $params['reference_no'],
            'notes' => (string) ($params['notes'] ?? 'Withdrawal Saldo'),
        ];

        // 1. Normalize request body
        $sortedBody = $body;
        $this->sortRecursive($sortedBody);
        $normalizedJson = json_encode($sortedBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // 2. Hash body with SHA-256
        $hashedBody = hash('sha256', $normalizedJson);
        $timestamp = (string) time();

        // 3. String to sign: METHOD:ENDPOINT:ACCESS_TOKEN:hashed_body:TIMESTAMP
        $stringToSign = "POST:{$endpoint}:{$token}:{$hashedBody}:{$timestamp}";
        $signature = hash_hmac('sha512', $stringToSign, $this->clientSecret);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'X-PARTNER-ID' => $this->partnerId,
                'X-Timestamp' => $timestamp,
                'X-Signature' => $signature,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}{$endpoint}", $body);

            $json = $response->json();
            Log::info('SingaPay Disburse Response', ['body' => $body, 'response' => $json]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'transaction_id' => $json['data']['transaction_id'] ?? null,
                    'status' => $json['data']['status'] ?? 'processing',
                    'data' => $json['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $json['message'] ?? $json['error']['message'] ?? 'Transfer gagal diproses oleh SingaPay',
                'data' => $json,
            ];
        } catch (Exception $e) {
            Log::error('SingapayDisbursementService::disburse exception: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Inquiry disbursement status
     */
    public function inquiryStatus(string $referenceNo): array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return ['success' => false, 'message' => 'Gagal mendapatkan akses token'];
        }

        try {
            $endpoint = "/api/v1.0/disbursement/{$this->accountId}/inquiry-status/{$referenceNo}";
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'X-PARTNER-ID' => $this->partnerId,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}{$endpoint}");

            $json = $response->json();

            return [
                'success' => $response->successful(),
                'status' => $json['data']['status'] ?? 'unknown',
                'data' => $json['data'] ?? [],
            ];
        } catch (Exception $e) {
            Log::error('SingapayDisbursementService::inquiryStatus exception: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Recursively sort array keys alphabetically
     */
    protected function sortRecursive(array &$arr): void
    {
        ksort($arr, SORT_STRING);
        foreach ($arr as &$value) {
            if (is_array($value)) {
                $this->sortRecursive($value);
            }
        }
    }
}
