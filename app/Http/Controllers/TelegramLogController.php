<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Setting;
use App\Models\TelegramLog;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramLogController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }

    public function index()
    {
        $telegramLogs = TelegramLog::with('customer')->latest('sent_at')->paginate(15);
        $settings = Setting::pluck('value', 'key')->toArray();
        $customers = Customer::whereNotNull('telegram_id')->orderBy('name')->get();
        $tokenConfigured = ! blank($settings['telegram_token'] ?? null) || ! blank(config('services.telegram.bot_token'));

        // Fetch actual webhook URL from Telegram API
        $webhookInfo = $this->telegramService->getWebhookInfo();
        $actualWebhookUrl = data_get($webhookInfo, 'result.url');

        return view('admin.telegram-logs.index', compact(
            'telegramLogs', 'settings', 'customers', 'tokenConfigured', 'actualWebhookUrl'
        ));
    }

    public function setWebhook(Request $request)
    {
        $validated = $request->validate([
            'webhook_url' => 'required|url',
        ]);

        $result = $this->telegramService->setWebhook($validated['webhook_url']);

        return redirect()->route('telegram-logs.index')
            ->with($result['ok'] ? 'success' : 'error', $result['reason']);
    }

    public function sendTestMessage(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'test_message' => 'required|string|max:1000',
        ]);

        $result = $this->telegramService->sendTestMessage(
            $validated['customer_id'] ?? null,
            $validated['test_message']
        );

        return redirect()->route('telegram-logs.index')
            ->with($result['ok'] ? 'success' : 'error', $result['reason']);
    }
}
