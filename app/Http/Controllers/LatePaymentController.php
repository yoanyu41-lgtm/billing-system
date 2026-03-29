<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\TelegramLog;
use Illuminate\Http\Request;

class LatePaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Installment::with('customer')
            ->where('remaining_balance', '>', 0)
            ->where('status', 'active');

        if ($user->role === 'user') {
            $query->where('created_by', $user->id);
        }

        $lateInstallments = $query->whereDoesntHave('payments', function ($q) {
            $q->where('payment_date', '>=', now()->subDays(30));
        })->get();

        return view('late-payments.index', compact('lateInstallments'));
    }

    public function sendReminder(Installment $installment)
    {
        // Simulate sending Telegram message
        TelegramLog::create([
            'customer_id' => $installment->customer_id,
            'message' => 'Payment reminder: Your remaining balance is $' . $installment->remaining_balance,
            'sent_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Reminder sent.');
    }
}
