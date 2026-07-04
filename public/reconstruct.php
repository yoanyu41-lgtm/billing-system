<?php
$dir = ".git/lost-found/other";
if (!is_dir($dir)) {
    $dir = "../.git/lost-found/other";
}

if (!is_dir($dir)) {
    die("Lost found directory not found.");
}

$files = scandir($dir);
$restored = [];

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $filePath = "$dir/$file";
    if (is_dir($filePath)) continue;
    
    $content = file_get_contents($filePath);
    $size = strlen($content);
    
    // 1. app/Console/Commands/ServeCommand.php
    if (strpos($content, 'namespace App\Console\Commands;') !== false && strpos($content, 'class ServeCommand') !== false) {
        restoreFile('app/Console/Commands/ServeCommand.php', $content, $file);
    }
    
    // 2. resources/views/installments/contract_print.blade.php
    // Starts with <!DOCTYPE html> and contains Poppins, Battambang, and and is about 21KB.
    if (strpos($content, '<html lang="km">') !== false && strpos($content, 'កិច្ចសន្យាបង់រំលស់') !== false && strpos($content, 'Poppins') !== false && strpos($content, 'Battambang') !== false && $size < 30000) {
        restoreFile('resources/views/installments/contract_print.blade.php', $content, $file);
    }
    
    // 3. public/save_qr_text.php
    if (strpos($content, 'qr_text') !== false && strpos($content, 'file_put_contents') !== false && strpos($content, 'qr_text.txt') !== false && $size < 2000) {
        restoreFile('public/save_qr_text.php', $content, $file);
    }
    
    // 4. test_khqr.php
    if (strpos($content, 'BakongKHQR') !== false || (strpos($content, 'KHQR') !== false && strpos($content, 'generate') !== false && strpos($content, 'test_khqr') !== false)) {
        restoreFile('test_khqr.php', $content, $file);
    }
    
    // 5. decode_branch.php
    if (strpos($content, 'decode_branch') !== false || (strpos($content, 'branch') !== false && strpos($content, 'decode') !== false && $size < 5000 && strpos($content, '<?php') !== false)) {
        restoreFile('decode_branch.php', $content, $file);
    }
    
    // 6. public/decode.php
    if (strpos($content, 'class Decode') !== false || (strpos($content, 'decode') !== false && strpos($content, 'Bakong') !== false && $size < 5000 && strpos($content, 'public/') !== false)) {
        // Just let it map based on other heuristics or if it contains decode logic
    }
}

function restoreFile($targetPath, $content, $hash) {
    global $restored;
    $dir = dirname($targetPath);
    if (!file_exists($dir) && !empty($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($targetPath, $content);
    $restored[$targetPath] = $hash;
    echo "Restored $targetPath from blob $hash\n";
}

echo "Restoration finished. Total files restored: " . count($restored) . "\n";
print_r($restored);
