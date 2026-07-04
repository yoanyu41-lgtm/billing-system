<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bankQr = \App\Models\Setting::where('key', 'company_bank_qr')->value('value');
if (!$bankQr) {
    echo json_encode(['error' => 'No static QR image found in settings']);
    exit;
}

$fullPath = storage_path('app/public/' . $bankQr);
if (!file_exists($fullPath)) {
    echo json_encode(['error' => "File does not exist: $fullPath"]);
    exit;
}

// Send to api.qrserver.com via multipart/form-data
$ch = curl_init('http://api.qrserver.com/v1/read-qr-code/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'file' => new CURLFile($fullPath)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

header('Content-Type: application/json');
if ($httpCode === 200) {
    $resData = json_decode($response, true);
    if (isset($resData[0]['symbol'][0]['data']) && !empty($resData[0]['symbol'][0]['data'])) {
        echo json_encode([
            'success' => true,
            'qr_payload' => $resData[0]['symbol'][0]['data']
        ], JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Could not decode QR. Response: ' . $response
        ], JSON_PRETTY_PRINT);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => "HTTP error $httpCode: $response"
    ], JSON_PRETTY_PRINT);
}
