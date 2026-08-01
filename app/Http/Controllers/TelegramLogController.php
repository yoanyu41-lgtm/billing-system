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

        // Broadcast stats
        $totalLinked = Customer::whereNotNull('telegram_id')->count();
        $totalCustomers = Customer::count();

        // Gather all configured QR codes for the QR selector
        $allQrList = [];
        $qrMap = [
            'qr_aba'       => ['label' => 'ABA Bank KHQR',     'icon' => 'aba'],
            'qr_acleda'    => ['label' => 'ACLEDA KHQR',       'icon' => 'acleda'],
            'qr_wing'      => ['label' => 'Wing KHQR',         'icon' => 'wing'],
            'qr_truemoney' => ['label' => 'TrueMoney KHQR',    'icon' => 'truemoney'],
            'qr_bakong'    => ['label' => 'Bakong KHQR',       'icon' => 'bakong'],
            'company_bank_qr' => ['label' => 'QR Code ធនាគារ (Default)', 'icon' => 'default'],
        ];
        foreach ($qrMap as $key => $meta) {
            $img = $settings[$key] ?? null;
            if (!empty($img)) {
                $allQrList[] = ['key' => $key, 'label' => $meta['label'], 'img' => $img];
            }
        }
        // Custom QR entries
        $customList = json_decode($settings['custom_qr_list'] ?? '[]', true) ?: [];
        foreach ($customList as $cItem) {
            if (!empty($cItem['key']) && !empty($cItem['label'])) {
                $img = $settings[$cItem['key']] ?? null;
                if (!empty($img)) {
                    $allQrList[] = ['key' => $cItem['key'], 'label' => $cItem['label'], 'img' => $img];
                }
            }
        }

        return view('admin.telegram-logs.index', compact(
            'telegramLogs', 'settings', 'customers', 'tokenConfigured', 'actualWebhookUrl',
            'totalLinked', 'totalCustomers', 'allQrList'
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
            'qr_key'      => 'nullable|string|max:100',
        ]);

        $customerId = $validated['customer_id'] ?? null;
        $message    = $validated['test_message'];
        $qrKey      = $validated['qr_key'] ?? null;

        // If a QR key was selected, send with QR photo
        if (!empty($qrKey)) {
            $settings = Setting::pluck('value', 'key')->toArray();
            $qrImg = $settings[$qrKey] ?? null;
            if (!empty($qrImg)) {
                $result = $this->telegramService->sendPhotoToCustomer(
                    (int) $customerId,
                    $qrImg,
                    $message
                );
                return redirect()->route('telegram-logs.index')
                    ->with($result['ok'] ? 'success' : 'error', $result['reason']);
            }
        }

        $result = $this->telegramService->sendTestMessage(
            $customerId ?? null,
            $message
        );

        return redirect()->route('telegram-logs.index')
            ->with($result['ok'] ? 'success' : 'error', $result['reason']);
    }
}
