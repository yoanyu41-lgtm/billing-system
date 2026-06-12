<?php
/**
 * Install Khmer OS Siemreap Font for DomPDF
 * Run: php install-khmer-font.php
 */

echo "=== Khmer OS Siemreap Font Installer ===\n\n";

$fontsDir = __DIR__ . '/storage/fonts';
if (!is_dir($fontsDir)) {
    mkdir($fontsDir, 0755, true);
}

// Try to download from alternative sources
$fontPath = $fontsDir . '/KhmerOSSiemreap.ttf';

echo "Attempting to download Khmer OS Siemreap...\n";

// Multiple download sources
$sources = [
    'https://www.khmeros.info/fonts/KhmerOSSiemreap.ttf',
    'http://sourceforge.net/projects/khmer/files/Fonts/KhmerOS/5.0/KhmerOSSiemreap.ttf/download',
];

$context = stream_context_create([
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    'http' => ['follow_location' => true, 'max_redirects' => 5, 'timeout' => 30]
]);

$downloaded = false;
foreach ($sources as $url) {
    echo "Trying: $url\n";
    $content = @file_get_contents($url, false, $context);
    
    if ($content && strlen($content) > 100000) {
        file_put_contents($fontPath, $content);
        echo "✓ Downloaded: " . number_format(strlen($content)) . " bytes\n";
        $downloaded = true;
        break;
    }
}

if (!$downloaded) {
    // Try using curl if available
    echo "\nTrying with curl...\n";
    $curlUrl = 'https://github.com/JamoCA/cfml-fusiondebug/raw/master/dompdf_fonts/KhmerOSSiemreap.ttf';
    $cmd = "curl -L -k -o \"$fontPath\" \"$curlUrl\" 2>&1";
    exec($cmd, $output, $return);
    
    if ($return === 0 && file_exists($fontPath) && filesize($fontPath) > 100000) {
        echo "✓ Downloaded via curl: " . number_format(filesize($fontPath)) . " bytes\n";
        $downloaded = true;
    }
}

if (!$downloaded) {
    echo "\n✗ Auto-download failed.\n\n";
    echo "=== Manual Installation ===\n";
    echo "1. Download from: https://www.khmeros.info/download.php\n";
    echo "2. Extract and copy 'KhmerOSSiemreap.ttf' to:\n";
    echo "   $fontPath\n";
    echo "3. Run: php load-khmer-siemreap.php\n";
    exit(1);
}

echo "\n✓✓✓ Font downloaded successfully! ✓✓✓\n";
echo "Next: Run 'php load-khmer-siemreap.php' to register it.\n";


