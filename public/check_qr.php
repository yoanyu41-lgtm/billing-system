<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

header('Content-Type: application/json');
echo json_encode(\App\Models\Setting::where('key', 'like', 'qr_%')->pluck('value', 'key'), JSON_PRETTY_PRINT);
