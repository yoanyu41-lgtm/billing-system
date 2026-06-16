<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Setting;
use App\Models\TelegramLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function sendToCustomer(?int $customerId, string $message): array
    {
        // Check if message is a reminder and we have a Bank QR Code configured
        $bankQr = Setting::where('key', 'company_bank_qr')->value('value');
        if ($bankQr && (str_starts_with(trim($message), '⏰') || str_contains(strtolower($message), 'reminder') || str_contains(strtolower($message), 'due'))) {
            return $this->sendPhotoToCustomer($customerId, $bankQr, $message);
        }

        $customer = Customer::find($customerId);

        if (! $customer) {
            return ['ok' => false, 'reason' => 'Customer not found.'];
        }

        if (blank($customer->telegram_id)) {
            TelegramLog::create([
                'customer_id' => $customer->id,
                'message' => '[NOT SENT] ' . $message . ' | Reason: Telegram ID is missing.',
                'sent_at' => now(),
            ]);

            return ['ok' => false, 'reason' => 'Customer Telegram ID is missing.'];
        }

        $token = $this->getToken();

        if (blank($token)) {
            TelegramLog::create([
                'customer_id' => $customer->id,
                'message' => '[NOT SENT] ' . $message . ' | Reason: Telegram token is not configured.',
                'sent_at' => now(),
            ]);

            return ['ok' => false, 'reason' => 'Telegram token is not configured.'];
        }

        // Look up ABA Pay link to append quick pay button to reminders
        $abaLink = Setting::where('key', 'company_aba_pay_link')->value('value');
        $replyMarkup = null;
        if ($abaLink && (str_starts_with(trim($message), '⏰') || str_contains(strtolower($message), 'reminder') || str_contains(strtolower($message), 'due'))) {
            $replyMarkup = [
                'inline_keyboard' => [
                    [
                        ['text' => '📲 ABA Instant Pay 💳', 'url' => $abaLink]
                    ]
                ]
            ];
        }

        $params = [
            'chat_id' => $customer->telegram_id,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", $params);

            if ($response->successful() && data_get($response->json(), 'ok') === true) {
                TelegramLog::create([
                    'customer_id' => $customer->id,
                    'message' => $message,
                    'sent_at' => now(),
                ]);

                return ['ok' => true, 'reason' => 'Telegram message sent successfully.'];
            }

            Log::warning('Telegram API returned an error.', [
                'customer_id' => $customer->id,
                'response' => $response->body(),
            ]);

            $apiError = data_get($response->json(), 'description', 'Telegram API rejected the request.');

            TelegramLog::create([
                'customer_id' => $customer->id,
                'message' => '[FAILED] ' . $message . ' | Reason: ' . $apiError,
                'sent_at' => now(),
            ]);

            return ['ok' => false, 'reason' => $apiError];
        } catch (\Throwable $e) {
            Log::error('Telegram send failed.', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);

            TelegramLog::create([
                'customer_id' => $customer->id,
                'message' => '[FAILED] ' . $message . ' | Reason: ' . $e->getMessage(),
                'sent_at' => now(),
            ]);

            return ['ok' => false, 'reason' => 'Telegram request failed.'];
        }
    }

    public function sendPhotoToCustomer(?int $customerId, string $photoPath, string $caption): array
    {
        $customer = Customer::find($customerId);

        if (! $customer) {
            return ['ok' => false, 'reason' => 'Customer not found.'];
        }

        if (blank($customer->telegram_id)) {
            TelegramLog::create([
                'customer_id' => $customer->id,
                'message' => '[NOT SENT PHOTO] ' . $caption . ' | Reason: Telegram ID is missing.',
                'sent_at' => now(),
            ]);

            return ['ok' => false, 'reason' => 'Customer Telegram ID is missing.'];
        }

        $token = $this->getToken();

        if (blank($token)) {
            TelegramLog::create([
                'customer_id' => $customer->id,
                'message' => '[NOT SENT PHOTO] ' . $caption . ' | Reason: Telegram token is not configured.',
                'sent_at' => now(),
            ]);

            return ['ok' => false, 'reason' => 'Telegram token is not configured.'];
        }

        try {
            $fullPath = storage_path('app/public/' . $photoPath);
            if (!file_exists($fullPath)) {
                // Fallback to text message if photo file is missing in storage
                $this->sendToCustomerTextOnly($customer, $caption, $token);
            }

            $abaLink = Setting::where('key', 'company_aba_pay_link')->value('value');
            $replyMarkup = null;
            if ($abaLink && (str_starts_with(trim($caption), '⏰') || str_contains(strtolower($caption), 'reminder') || str_contains(strtolower($caption), 'due'))) {
                $replyMarkup = [
                    'inline_keyboard' => [
                        [
                            ['text' => '📲 ABA Instant Pay 💳', 'url' => $abaLink]
                        ]
                    ]
                ];
            }

            $params = [
                'chat_id' => $customer->telegram_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
            ];

            if ($replyMarkup) {
                $params['reply_markup'] = json_encode($replyMarkup);
            }

            $response = Http::attach(
                'photo', file_get_contents($fullPath), basename($photoPath)
            )->post("https://api.telegram.org/bot{$token}/sendPhoto", $params);

            if ($response->successful() && data_get($response->json(), 'ok') === true) {
                TelegramLog::create([
                    'customer_id' => $customer->id,
                    'message' => '[PHOTO SENT] ' . $caption,
                    'sent_at' => now(),
                ]);

                return ['ok' => true, 'reason' => 'Telegram message with photo sent successfully.'];
            }

            Log::warning('Telegram API returned an error for photo.', [
                'customer_id' => $customer->id,
                'response' => $response->body(),
            ]);

            $apiError = data_get($response->json(), 'description', 'Telegram API rejected the request.');

            TelegramLog::create([
                'customer_id' => $customer->id,
                'message' => '[FAILED PHOTO] ' . $caption . ' | Reason: ' . $apiError,
                'sent_at' => now(),
            ]);

            return ['ok' => false, 'reason' => $apiError];
        } catch (\Throwable $e) {
            Log::error('Telegram send photo failed.', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);

            TelegramLog::create([
                'customer_id' => $customer->id,
                'message' => '[FAILED PHOTO] ' . $caption . ' | Reason: ' . $e->getMessage(),
                'sent_at' => now(),
            ]);

            return ['ok' => false, 'reason' => 'Telegram request failed.'];
        }
    }

    private function sendToCustomerTextOnly(Customer $customer, string $message, string $token): array
    {
        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $customer->telegram_id,
                    'text' => $message,
                ]);

            if ($response->successful() && data_get($response->json(), 'ok') === true) {
                TelegramLog::create([
                    'customer_id' => $customer->id,
                    'message' => $message,
                    'sent_at' => now(),
                ]);

                return ['ok' => true, 'reason' => 'Telegram message sent successfully.'];
            }

            return ['ok' => false, 'reason' => 'Text fallback failed.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'reason' => $e->getMessage()];
        }
    }

    public function setWebhook(?string $webhookUrl = null): array
    {
        $token = $this->getToken();
        $webhookUrl = $webhookUrl ?: url('/api/v1/telegram/webhook');

        if (blank($token)) {
            return ['ok' => false, 'reason' => 'Telegram token is not configured.'];
        }

        if (! str_starts_with($webhookUrl, 'https://')) {
            return ['ok' => false, 'reason' => 'Webhook URL must be a public HTTPS URL.'];
        }

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post("https://api.telegram.org/bot{$token}/setWebhook", [
                    'url' => $webhookUrl,
                ]);

            if ($response->successful() && data_get($response->json(), 'ok') === true) {
                return ['ok' => true, 'reason' => 'Webhook set successfully.'];
            }

            Log::warning('Telegram webhook setup failed.', ['response' => $response->body()]);

            return ['ok' => false, 'reason' => 'Telegram rejected the webhook URL.'];
        } catch (\Throwable $e) {
            Log::error('Telegram webhook setup error.', ['error' => $e->getMessage()]);

            return ['ok' => false, 'reason' => 'Webhook request failed.'];
        }
    }

    public function sendTestMessage(?int $customerId = null, ?string $message = null): array
    {
        $customerId = $customerId ?: Customer::whereNotNull('telegram_id')->value('id');
        $message = $message ?: '✅ Telegram test message from CityTech Billing System.';

        if (! $customerId) {
            return ['ok' => false, 'reason' => 'No customer with Telegram ID was found.'];
        }

        return $this->sendToCustomer((int) $customerId, $message);
    }

    public function getWebhookInfo(): array
    {
        $token = $this->getToken();
        if (blank($token)) {
            return ['ok' => false, 'reason' => 'Telegram token is not configured.'];
        }

        try {
            $response = Http::timeout(10)->get("https://api.telegram.org/bot{$token}/getWebhookInfo");
            if ($response->successful()) {
                return $response->json();
            }
            return ['ok' => false, 'reason' => 'Failed to fetch webhook info.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'reason' => $e->getMessage()];
        }
    }

    private function getToken(): ?string
    {
        return Setting::where('key', 'telegram_token')->value('value') ?: config('services.telegram.bot_token');
    }
}
