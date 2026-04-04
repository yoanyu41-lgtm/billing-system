<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }

    public function webhook(Request $request)
    {
        $update = $request->all();

        if (isset($update['message'])) {
            $chatId = data_get($update, 'message.chat.id');
            $text = trim((string) data_get($update, 'message.text', ''));

            if ($text === '/start') {
                $customer = Customer::whereNull('telegram_id')->first();

                if ($customer) {
                    $customer->update(['telegram_id' => $chatId]);
                    $this->replyToChat($chatId, '✅ Telegram linked successfully for ' . $customer->name . '.');
                } else {
                    $this->replyToChat($chatId, 'No customer is waiting to be linked right now.');
                }
            }

            if ($text === '/id') {
                $this->replyToChat($chatId, 'Your Telegram chat ID is: ' . $chatId);
            }
        }

        return response('OK');
    }

    public function sendMessage($customerId, $message)
    {
        return $this->telegramService->sendToCustomer($customerId, $message);
    }

    private function replyToChat($chatId, string $message): void
    {
        $token = config('services.telegram.bot_token') ?: \App\Models\Setting::where('key', 'telegram_token')->value('value');

        if (blank($token) || blank($chatId)) {
            return;
        }

        Http::asForm()->timeout(15)->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }
}
