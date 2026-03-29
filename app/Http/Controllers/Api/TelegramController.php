<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        $update = $request->all();

        if (isset($update['message'])) {
            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'];

            if ($text === '/start') {
                // link the first non-linked customer for demo/test purposes
                $customer = Customer::whereNull('telegram_id')->first();
                if ($customer) {
                    $customer->update(['telegram_id' => $chatId]);
                }
            }
        }

        return response('OK');
    }

    public function sendMessage($customerId, $message)
    {
        $customer = Customer::find($customerId);
        if ($customer && $customer->telegram_id) {
            // Use Telegram API to send message
            // For now, just log
            \App\Models\TelegramLog::create([
                'customer_id' => $customerId,
                'message' => $message,
                'sent_at' => now(),
            ]);
        }
    }
}
