<?php

namespace App\Console\Commands;

use App\Models\Installment;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendDuePaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:due-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Telegram reminders when installment due date arrives.';

    public function __construct(protected TelegramService $telegramService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        $dueInstallments = Installment::with('customer')
            ->where('status', 'active')
            ->where('remaining_balance', '>', 0)
            ->where(function ($query) use ($today) {
                $query->whereNotNull('next_due_date')->where('next_due_date', '<=', $today)
                    ->orWhereNull('next_due_date');
            })
            ->get();

        if ($dueInstallments->isEmpty()) {
            $this->info('No due reminders to send today.');
            return 0;
        }

        $sent = 0;

        foreach ($dueInstallments as $installment) {
            $message = "⏰ Payment due reminder\n"
                . "Customer: {$installment->customer->name}\n"
                . "Remaining balance: $" . number_format($installment->remaining_balance, 2) . "\n"
                . "Please pay your monthly installment today or contact us for help.";

            $result = $this->telegramService->sendToCustomer($installment->customer_id, $message);

            if ($result['ok']) {
                $nextDue = $installment->next_due_date ? Carbon::parse($installment->next_due_date)->addMonth() : Carbon::today()->addMonth();

                $installment->update([
                    'last_reminder_sent_at' => now(),
                    'next_due_date' => $nextDue->toDateString(),
                ]);

                $sent++;
                $this->info("Reminder sent to customer #{$installment->customer_id} for installment #{$installment->id}.");
            } else {
                $this->warn("Failed to send reminder for installment #{$installment->id}: {$result['reason']}");
            }
        }

        $this->info("Done. Total reminders sent: {$sent}");
        return 0;
    }
}
