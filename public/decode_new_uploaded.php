<?php
header('Content-Type: text/plain; charset=utf-8');

$filePath = "../storage/app/public/company/Yg1Ft3FWFXHDi2KXRNNwU413jEbSRdgi2FtV7QwA.jpg";
if (!file_exists($filePath)) {
    $filePath = "storage/app/public/company/Yg1Ft3FWFXHDi2KXRNNwU413jEbSRdgi2FtV7QwA.jpg";
}

if (!file_exists($filePath)) {
    die("File does not exist.\n");
}

echo "Decoding image: $filePath...\n";

$ch = curl_init('https://zxing.org/w/decode');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'f' => new CURLFile(realpath($filePath))
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    if (preg_match('/<pre>(.*?)<\/pre>/s', $response, $matches)) {
        echo "SUCCESS! Decoded QR Payload:\n";
        echo trim(html_entity_decode($matches[1])) . "\n";
    } else {
        echo "Failed to find decoded text in ZXing response. Response preview:\n";
        echo substr(strip_tags($response), 0, 1000) . "\n";
    }
} else {
    echo "ZXing request failed with HTTP code $httpCode. Response: " . substr(strip_tags($response), 0, 500) . "\n";
}
