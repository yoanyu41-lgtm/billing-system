<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== FIND DELETED FILES ===\n\n";

$dir = ".git/lost-found/other";
if (!is_dir($dir)) {
    $dir = "../.git/lost-found/other";
}

if (!is_dir($dir)) {
    die("Lost found directory not found.");
}

$files = scandir($dir);
$matches = [];

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $filePath = "$dir/$file";
    $content = file_get_contents($filePath);
    $size = filesize($filePath);
    
    // Check for contract view
    if (strpos($content, 'contract') !== false || strpos($content, 'កិច្ចសន្យា') !== false || strpos($content, 'installments') !== false) {
        if (strpos($content, '@extends') !== false || strpos($content, '<div') !== false) {
            $matches['contract_print.blade.php'][] = [
                'hash' => $file,
                'size' => $size,
                'preview' => substr($content, 0, 500)
            ];
        }
    }
    
    // Check for QR text script
    if (strpos($content, 'qr_text') !== false || strpos($content, 'save_qr') !== false || strpos($content, 'qr_image') !== false) {
        if (strpos($content, '<?php') !== false) {
            $matches['save_qr_text.php / QR script'][] = [
                'hash' => $file,
                'size' => $size,
                'preview' => substr($content, 0, 500)
            ];
        }
    }

    // Check for KHQR / Bakong / CRC test scripts
    if (strpos($content, 'KHQR') !== false || strpos($content, 'Bakong') !== false || strpos($content, 'crc16') !== false || strpos($content, 'crc') !== false) {
        if (strpos($content, '<?php') !== false) {
            $matches['test_khqr.php / crc script'][] = [
                'hash' => $file,
                'size' => $size,
                'preview' => substr($content, 0, 500)
            ];
        }
    }
    
    // Check for ServeCommand
    if (strpos($content, 'class ServeCommand') !== false) {
        $matches['ServeCommand.php'][] = [
            'hash' => $file,
            'size' => $size,
            'preview' => substr($content, 0, 500)
        ];
    }
}

$output = "";
foreach ($matches as $type => $list) {
    $output .= "==================================================\n";
    $output .= "POTENTIAL MATCHES FOR: $type\n";
    $output .= "==================================================\n";
    foreach ($list as $i => $item) {
        $output .= "[$i] Hash: {$item['hash']} | Size: {$item['size']} bytes\n";
        $output .= "--- PREVIEW ---\n";
        $output .= trim($item['preview']) . "\n";
        $output .= "--------------------------------------------------\n\n";
    }
}

file_put_contents(__DIR__ . '/find_deleted_output.txt', $output);
echo "Written to find_deleted_output.txt successfully";
