<?php
$filePath = "C:\\Users\\Yu\\.gemini\\antigravity-ide\\brain\\198f8260-8a7d-4a58-a24d-f89fc7d426a4\\media__1782646092109.png";
$imgBase64 = '';
if (file_exists($filePath)) {
    $imageData = base64_encode(file_get_contents($filePath));
    $mimeType = mime_content_type($filePath);
    $imgBase64 = 'data:' . $mimeType . ';base64,' . $imageData;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Decode Telegram QR</title>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <style>
        body { font-family: sans-serif; padding: 20px; text-align: center; }
        img { max-width: 300px; border: 1px solid #ccc; padding: 5px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; word-break: break-all; white-space: pre-wrap; font-size: 1.1rem; }
    </style>
</head>
<body>
    <h1>Decoding Telegram QR Screenshot...</h1>
    <?php if ($imgBase64): ?>
        <img id="qrImg" src="<?php echo $imgBase64; ?>">
        <canvas id="canvas" style="display:none;"></canvas>
        <h3>Decoded Result:</h3>
        <pre id="result">Decoding...</pre>
    <?php else: ?>
        <div style="color:red;">File not found!</div>
    <?php endif; ?>

    <script>
        const base64Data = "<?php echo $imgBase64; ?>";
        if (base64Data) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.getElementById('canvas');
                const ctx = canvas.getContext('2d');
                const padding = 50;
                canvas.width = img.width + padding * 2;
                canvas.height = img.height + padding * 2;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, padding, padding);
                
                let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                let code = jsQR(imageData.data, imageData.width, imageData.height);
                
                if (!code) {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);
                    imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    code = jsQR(imageData.data, imageData.width, imageData.height);
                }

                if (code) {
                    document.getElementById('result').innerText = code.data;
                    
                    // Send to backend via save_qr_text.php
                    fetch('/save_qr_text.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ qr_text: code.data })
                    });
                } else {
                    document.getElementById('result').innerText = "Failed to locate QR code in this image.";
                }
            };
            img.src = base64Data;
        }
    </script>
</body>
</html>
