<?php

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

$bankQr = Setting::where('key', 'company_bank_qr')->value('value');
$payload = Setting::where('key', 'company_bank_qr_payload')->value('value');

echo "DB company_bank_qr: " . ($bankQr ?: "EMPTY") . "\n";
echo "DB company_bank_qr_payload: " . ($payload ?: "EMPTY") . "\n";
