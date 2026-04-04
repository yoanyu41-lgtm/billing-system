<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('payments:due-reminders', function () {
    $command = app(\App\Console\Commands\SendDuePaymentReminders::class);
    $command->handle();
})->purpose('Send Telegram reminders for due installment payments.');
