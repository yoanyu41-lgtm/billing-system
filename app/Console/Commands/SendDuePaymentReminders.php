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
        $today = Carbon::today();

        $installments = Installment::with(['customer', 'product'])
            ->where('status', 'active')
            ->where('remaining_balance', '>', 0)
            ->get();

        if ($installments->isEmpty()) {
            $this->info('No active installments to check for reminders today.');
            return 0;
        }

        $sent = 0;

        foreach ($installments as $installment) {
            // Guard: Only send one reminder per day
            if ($installment->last_reminder_sent_at && Carbon::parse($installment->last_reminder_sent_at)->isToday()) {
                continue;
            }

            $schedule = $installment->getPaymentSchedule();
            $currentMonthRow = collect($schedule)->first(fn($row) => $row['status'] !== 'paid');

            if (!$currentMonthRow) {
                continue;
            }

            $dueDate = Carbon::parse($currentMonthRow['due_date']);
            $daysDiff = $today->diffInDays($dueDate, false);

            $message = null;
            $monthNo = $currentMonthRow['month'];
            $khmerMonth = $this->toKhmerNumerals($monthNo);
            $amount = $currentMonthRow['amount'];
            $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
            $amountRiel = round($amount * $exchangeRate);
            
            $formattedAmount = number_format($amount, 2);
            $formattedRiel = number_format($amountRiel);
            $formattedDueDate = $dueDate->format('d-m-Y');

            if ($daysDiff === 3) {
                // Due Soon (3 days before)
                $message = "⏰ *សេចក្តីជូនដំណឹងអំពីការបង់ប្រាក់ (រំលឹកមុនថ្ងៃកំណត់)*\n\n"
                    . "សូមជម្រាបជូនអតិថិជន *{$installment->customer->name}*៖\n"
                    . "សូមរំលឹកថា ការបង់ប្រាក់សម្រាប់គម្រោងបង់រំលស់ផលិតផល៖ *{$installment->product->name}* (ខែទី *{$khmerMonth}*) នឹងមកដល់ក្នុងរយៈពេល *៣ថ្ងៃទៀត* គឺនៅថ្ងៃទី *{$formattedDueDate}*។\n"
                    . "• ទឹកប្រាក់ត្រូវបង់៖ *${$formattedAmount}* (ឬ ~ *{$formattedRiel}* ៛)\n\n"
                    . "សូមលោកអ្នករៀបចំថវិកាទុកជាមុន។ សូមអរគុណ! 🙏";
            } elseif ($daysDiff === 0) {
                // Due Date (On due date)
                $message = "⏰ *សេចក្តីជូនដំណឹងអំពីការបង់ប្រាក់ (ដល់ថ្ងៃកំណត់បង់)*\n\n"
                    . "សូមជម្រាបជូនអតិថិជន *{$installment->customer->name}*៖\n"
                    . "ការបង់ប្រាក់សម្រាប់គម្រោងបង់រំលស់ផលិតផល៖ *{$installment->product->name}* (ខែទី *{$khmerMonth}*) គឺ *ដល់ថ្ងៃកំណត់បង់នៅថ្ងៃនេះហើយ* (ថ្ងៃទី *{$formattedDueDate}*)។\n"
                    . "• ទឹកប្រាក់ត្រូវបង់៖ *${$formattedAmount}* (ឬ ~ *{$formattedRiel}* ៛)\n\n"
                    . "សូមលោកអ្នកធ្វើការទូទាត់ប្រាក់តាមរយៈ Link ឬ QR Code ធនាគារ ABA ខាងក្រោម រួចផ្ញើរូបភាពបង្កាន់ដៃត្រឡប់មកវិញ។ សូមអរគុណ! 🙏";
            } elseif ($daysDiff === -1) {
                // Overdue 1 Day
                $message = "⏰ *សេចក្តីជូនដំណឹងអំពីការបង់ប្រាក់ (ហួសថ្ងៃកំណត់)*\n\n"
                    . "សូមជម្រាបជូនអតិថិជន *{$installment->customer->name}*៖\n"
                    . "ការបង់ប្រាក់សម្រាប់គម្រោងបង់រំលស់ផលិតផល៖ *{$installment->product->name}* (ខែទី *{$khmerMonth}*) គឺ *ហួសថ្ងៃកំណត់បង់ចំនួន ១ថ្ងៃហើយ* (ថ្ងៃទី *{$formattedDueDate}*)។\n"
                    . "• ទឹកប្រាក់ត្រូវបង់៖ *${$formattedAmount}* (ឬ ~ *{$formattedRiel}* ៛)\n"
                    . "• ការផាកពិន័យ៖ គ្មាន\n\n"
                    . "សូមលោកអ្នកធ្វើការទូទាត់ប្រាក់ឱ្យបានលឿនបំផុត ដើម្បីជៀសវាងការផាកពិន័យ $5 ក្នុងមួយថ្ងៃ (សម្រាប់ការយឺតយ៉ាវចាប់ពី ៦ថ្ងៃឡើងទៅ)។ សូមអរគុណ! 🙏";
            } elseif ($daysDiff === -3) {
                // Overdue 3 Days
                $message = "⏰ *សេចក្តីជូនដំណឹងអំពីការបង់ប្រាក់ (ហួសថ្ងៃកំណត់)*\n\n"
                    . "សូមជម្រាបជូនអតិថិជន *{$installment->customer->name}*៖\n"
                    . "ការបង់ប្រាក់សម្រាប់គម្រោងបង់រំលស់ផលិតផល៖ *{$installment->product->name}* (ខែទី *{$khmerMonth}*) គឺ *ហួសថ្ងៃកំណត់បង់ចំនួន ៣ថ្ងៃហើយ* (ថ្ងៃទី *{$formattedDueDate}*)។\n"
                    . "• ទឹកប្រាក់ត្រូវបង់៖ *${$formattedAmount}* (ឬ ~ *{$formattedRiel}* ៛)\n"
                    . "• ការផាកពិន័យ៖ គ្មាន (ការផាកពិន័យ $5/ថ្ងៃ នឹងចាប់ផ្តើមអនុវត្តចាប់ពីថ្ងៃទី៦ នៃការយឺតយ៉ាវ)\n\n"
                    . "សូមលោកអ្នកធ្វើការទូទាត់ប្រាក់ឱ្យបានលឿនបំផុត។ សូមអរគុណ! 🙏";
            }

            if ($message) {
                $result = $this->telegramService->sendToCustomer($installment->customer_id, $message);

                if ($result['ok']) {
                    $installment->update([
                        'last_reminder_sent_at' => now(),
                    ]);
                    $sent++;
                    $this->info("Reminder sent to customer #{$installment->customer_id} for installment #{$installment->id} (diff: {$daysDiff}).");
                } else {
                    $this->warn("Failed to send reminder for installment #{$installment->id}: {$result['reason']}");
                }
            }
        }

        $this->info("Done. Total reminders sent: {$sent}");
        return 0;
    }

    private function toKhmerNumerals($num): string
    {
        $khmerDigits = ['0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤', '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩'];
        return strtr((string)$num, $khmerDigits);
    }
}
