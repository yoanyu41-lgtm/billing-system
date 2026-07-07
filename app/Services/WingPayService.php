<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WingPayService
{
    /**
     * Get Wing Pay Credentials from config or settings.
     */
    public function getCredentials(): array
    {
        return [
            'merchant_id' => env('WING_PAY_MERCHANT_ID', Setting::where('key', 'wing_pay_merchant_id')->value('value')),
            'secret_key'  => env('WING_PAY_SECRET_KEY', Setting::where('key', 'wing_pay_secret_key')->value('value')),
            'api_url'     => env('WING_PAY_API_URL', Setting::where('key', 'wing_pay_api_url')->value('value') ?? 'https://sandbox-api.wingmoney.com/v1/payments'),
        ];
    }

    /**
     * Check if Wing Pay credentials are fully configured.
     */
    public function isConfigured(): bool
    {
        $creds = $this->getCredentials();
        return !empty($creds['merchant_id']) && !empty($creds['secret_key']);
    }

    /**
     * Generate secure HMAC-SHA256 signature for Wing Pay.
     */
    public function generateSignature(array $data, string $secretKey): string
    {
        // Sort keys to maintain strict sequence
        ksort($data);
        
        $signString = '';
        foreach ($data as $key => $value) {
            if ($key === 'signature') continue;
            $signString .= $key . '=' . $value . '&';
        }
        $signString = rtrim($signString, '&');

        return hash_hmac('sha256', $signString, $secretKey);
    }

    /**
     * Initiate a transaction session with Wing Bank and return checkout URL.
     */
    public function createCheckoutSession(array $paymentData): ?string
    {
        $creds = $this->getCredentials();
        if (!$this->isConfigured()) {
            Log::warning('Wing Pay is not configured. Falling back.');
            return null;
        }

        $params = [
            'merchant_id' => $creds['merchant_id'],
            'order_id'    => 'WING-INV-' . $paymentData['payment_id'] . '-' . time(),
            'amount'      => number_format((float) $paymentData['amount'], 2, '.', ''),
            'currency'    => strtoupper($paymentData['currency'] ?? 'USD'),
            'return_url'  => route('payments.wing-return'),
            'cancel_url'  => route('payments.create'),
            'remark'      => 'Installment payment for plan #' . $paymentData['installment_id'],
        ];

        // Generate HMAC Signature
        $params['signature'] = $this->generateSignature($params, $creds['secret_key']);

        try {
            Log::info('Initiating Wing Pay checkout session', ['order_id' => $params['order_id']]);
            
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($creds['api_url'] . '/checkout', $params);

            if ($response->successful() && isset($response->json()['checkout_url'])) {
                return $response->json()['checkout_url'];
            }

            Log::error('Wing Pay checkout session failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
        } catch (\Throwable $e) {
            Log::error('Exception during Wing Pay session initiation', [
                'message' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Verify callback signature from Wing Pay webhook.
     */
    public function verifyCallback(array $payload): bool
    {
        $creds = $this->getCredentials();
        if (!isset($payload['signature'])) {
            return false;
        }

        $incomingSignature = $payload['signature'];
        
        // Regenerate signature to verify
        $expectedSignature = $this->generateSignature($payload, $creds['secret_key']);

        return hash_equals($expectedSignature, $incomingSignature);
    }
}
