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

            Log::warning('Telegram API returned an error.', [
                'customer_id' => $customer->id,
                'response' => $response->body(),
            ]);

            TelegramLog::create([
                'customer_id' => $customer->id,
                'message' => '[FAILED] ' . $message . ' | Reason: Telegram API rejected the request.',
                'sent_at' => now(),
            ]);

            return ['ok' => false, 'reason' => 'Telegram API rejected the request.'];
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

    public function setWebhook(?string $webhookUrl = null): array
    {
        $token = $this->getToken();
        $webhookUrl = $webhookUrl ?: url('/api/telegram/webhook');

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

    private function getToken(): ?string
    {
        return Setting::where('key', 'telegram_token')->value('value') ?: config('services.telegram.bot_token');
    }
}
