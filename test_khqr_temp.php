<?php

define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\KhqrService;
use App\Services\TelegramService;

echo "=== TESTING KHQR SERVICE ===\n";

$khqr = new KhqrService();
$basePayload = "00020101021129350013kh.gov.bakong0114069244286@wing520459995303840540510.005802KH5907Yu YOAN6009Siem Reap6304";

// Test parsing
$tags = $khqr->parsePayload($basePayload);
echo "Parsed Tags:\n";
print_r($tags);

// Test dynamic generation
$amount = 125.50;
$currency = "USD";
$dynamicPayload = $khqr->generatePayload($basePayload, $amount, $currency);
echo "\nDynamic Payload: $dynamicPayload\n";

// Parse dynamic and verify
$dynamicTags = $khqr->parsePayload($dynamicPayload);
echo "Dynamic Tags Amount (Tag 54): " . ($dynamicTags['54'] ?? 'NOT FOUND') . "\n";
echo "Dynamic Tags Currency (Tag 53): " . ($dynamicTags['53'] ?? 'NOT FOUND') . "\n";
echo "Dynamic Tags Initiation Method (Tag 01): " . ($dynamicTags['01'] ?? 'NOT FOUND') . "\n";

// Test Telegram amount extraction
echo "\n=== TESTING TELEGRAM EXTRACTOR ===\n";
$telegram = new TelegramService();

$msg1 = "⏰ *សេចក្តីជូនដំណឹងអំពីការបង់ប្រាក់*\n• ទឹកប្រាក់ត្រូវបង់៖ *$150.75* (ឬ ~ *618,000* ៛)";
$msg2 = "⏰ *Payment Request*\n• Amount Due: *$1,250.00* (or ~ *5,125,000* KHR)";
$msg3 = "Please send 40,000 ៛ to our account.";

[$amt1, $cur1] = $telegram->extractAmountAndCurrency($msg1);
[$amt2, $cur2] = $telegram->extractAmountAndCurrency($msg2);
[$amt3, $cur3] = $telegram->extractAmountAndCurrency($msg3);

echo "Msg 1: amount = $amt1, currency = $cur1\n";
echo "Msg 2: amount = $amt2, currency = $cur2\n";
echo "Msg 3: amount = $amt3, currency = $cur3\n";

echo "\n=== DONE ===\n";
