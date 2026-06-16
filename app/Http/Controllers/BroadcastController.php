<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }

    public function index()
    {
        $totalLinked = Customer::whereNotNull('telegram_id')->count();
        $totalCustomers = Customer::count();

        return view('admin.broadcast.index', compact('totalLinked', 'totalCustomers'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:5',
        ]);

        $customers = Customer::whereNotNull('telegram_id')->get();

        if ($customers->isEmpty()) {
            return redirect()->back()->with('error', 'គ្មានអតិថិជនដែលបានភ្ជាប់ Telegram សម្រាប់ទទួលសារផ្សព្វផ្សាយឡើយ (No linked customers found).');
        }

        $successCount = 0;
        $failCount = 0;
        $messageText = $request->message;

        foreach ($customers as $customer) {
            $result = $this->telegramService->sendToCustomer($customer->id, $messageText);
            if ($result['ok']) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        $status = $successCount > 0 ? 'success' : 'error';
        $flashMessage = "📢 ផ្សព្វផ្សាយរួចរាល់៖ ផ្ញើជោគជ័យចំនួន {$successCount} នាក់ និងបរាជ័យចំនួន {$failCount} នាក់។";

        return redirect()->back()->with($status, $flashMessage);
    }
}
