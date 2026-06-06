<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LatePaymentController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }
    public function index()
    {
        $user = auth()->user();
        $query = Installment::with('customer')
            ->where('remaining_balance', '>', 0)
            ->where('status', 'active');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        $lateInstallments = $query->whereDoesntHave('payments', function ($q) {
            $q->where('payment_date', '>=', now()->subDays(30));
        })->get();

        return view('late-payments.index', compact('lateInstallments'));
    }

    public function sendReminder(Installment $installment)
    {
        $message = "⏰ Payment reminder\n"
            . "Customer: {$installment->customer->name}\n"
            . "Remaining balance: $" . number_format($installment->remaining_balance, 2) . "\n"
            . "Please contact the shop if you already paid.";

        $telegramResult = $this->telegramService->sendToCustomer($installment->customer_id, $message);

        $flashMessage = $telegramResult['ok']
            ? 'Reminder sent via Telegram.'
            : 'Reminder saved, but Telegram was not sent: ' . $telegramResult['reason'];

        return redirect()->back()->with($telegramResult['ok'] ? 'success' : 'error', $flashMessage);
    }

    public function sendDueDateReminders()
    {
        $today = Carbon::today()->toDateString();

        $installments = Installment::with('customer')
            ->where('status', 'active')
            ->where('remaining_balance', '>', 0)
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '<=', $today)
            ->get();

        $sent = 0;

        foreach ($installments as $installment) {
            $message = "⏰ Payment due today\n"
                . "Customer: {$installment->customer->name}\n"
                . "Remaining balance: $" . number_format($installment->remaining_balance, 2) . "\n"
                . "Please make payment now to avoid penalties.";

            $result = $this->telegramService->sendToCustomer($installment->customer_id, $message);

            if ($result['ok']) {
                $installment->update([
                    'last_reminder_sent_at' => now(),
                    'next_due_date' => Carbon::parse($installment->next_due_date)->addMonth()->toDateString(),
                ]);
                $sent++;
            }
        }

        $status = $sent > 0 ? 'success' : 'error';
        $message = $sent > 0 ? "{$sent} due reminders sent." : 'No due reminders sent.';

        return redirect()->back()->with($status, $message);
    }
}
