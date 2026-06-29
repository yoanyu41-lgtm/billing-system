<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bankQr = \App\Models\Setting::where('key', 'company_bank_qr')->value('value');
$bankQrBase64 = '';
$errorMsg = '';

if ($bankQr) {
    $fullPath = storage_path('app/public/' . $bankQr);
    if (file_exists($fullPath)) {
        $imageData = base64_encode(file_get_contents($fullPath));
        $mimeType = mime_content_type($fullPath);
        $bankQrBase64 = 'data:' . $mimeType . ';base64,' . $imageData;
    } else {
        $errorMsg = "File does not exist at storage path: " . $fullPath;
    }
} else {
    $errorMsg = "No bank QR code image configured in settings.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Decode QR</title>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            color: #333;
        }
        .container {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .qr-preview {
            max-width: 250px;
            border: 1px solid #cbd5e1;
            padding: 8px;
            border-radius: 8px;
            background: #fff;
            margin: 16px auto;
            display: block;
        }
        .result-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 16px;
            font-weight: bold;
            font-size: 1.1rem;
            color: #1e3a8a;
            word-break: break-all;
            margin-top: 16px;
        }
        .error-box {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Decoding QR Code...</h1>
        
        <?php if ($bankQrBase64): ?>
            <div>
                <h3>Your QR Code Image:</h3>
                <img src="<?php echo $bankQrBase64; ?>" class="qr-preview" alt="QR Preview">
            </div>
        <?php else: ?>
            <div class="error-box">
                Failed to load image file. <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>

        <canvas id="canvas" style="display:none;"></canvas>
        <div id="result" class="result-box">Waiting to decode...</div>
    </div>

    <script>
        const base64Data = "<?php echo $bankQrBase64; ?>";
        if (base64Data) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.getElementById('canvas');
                const ctx = canvas.getContext('2d');
                
                // Try with white padding first (helps closely cropped QRs)
                const padding = 50;
                canvas.width = img.width + padding * 2;
                canvas.height = img.height + padding * 2;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, padding, padding);
                
                let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                let code = jsQR(imageData.data, imageData.width, imageData.height);
                
                // Fallback to original without padding if it fails
                if (!code) {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);
                    imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    code = jsQR(imageData.data, imageData.width, imageData.height);
                }
                
                if (code) {
                    document.getElementById('result').innerHTML = `
                        <div style="font-size: 0.9rem; color: #475569; margin-bottom: 8px;">Bakong Payload Found:</div>
                        <div style="font-family: monospace; user-select: all; background: #fff; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">${code.data}</div>
                        <div style="font-size: 0.8rem; color: #059669; margin-top: 8px;">(Double-click or drag to select and copy the code above)</div>
                    `;
                } else {
                    // Fallback to online API if jsQR client-side decoding fails
                    document.getElementById('result').innerText = "Local decoding failed. Trying online decoder...";
                    
                    fetch(base64Data)
                    .then(res => res.blob())
                    .then(blob => {
                        const formData = new FormData();
                        formData.append('file', blob, 'qrcode.jpg');
                        return fetch('https://api.qrserver.com/v1/read-qr-code/', {
                            method: 'POST',
                            body: formData
                        });
                    })
                    .then(res => res.json())
                    .then(apiData => {
                        if (apiData && apiData[0] && apiData[0].symbol && apiData[0].symbol[0] && apiData[0].symbol[0].data) {
                            const qrText = apiData[0].symbol[0].data;
                            document.getElementById('result').innerHTML = `
                                <div style="font-size: 0.9rem; color: #475569; margin-bottom: 8px;">Bakong Payload Found (via online decoder):</div>
                                <div style="font-family: monospace; user-select: all; background: #fff; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">${qrText}</div>
                                <div style="font-size: 0.8rem; color: #059669; margin-top: 8px;">(Double-click or drag to select and copy the code above)</div>
                            `;
                        } else {
                            document.getElementById('result').innerText = "Could not decode QR code. Please make sure the uploaded image is a clear and valid QR code.";
                        }
                    })
                    .catch(err => {
                        document.getElementById('result').innerText = "Error calling online decoder. Please check your internet connection.";
                        console.error(err);
                    });
                }
            };
            img.onerror = function() {
                document.getElementById('result').innerText = "Failed to process image data.";
            };
            img.src = base64Data;
        } else {
            document.getElementById('result').innerText = "No image data available to decode.";
        }
    </script>
</body>
</html>
