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
$candidates = [];

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $filePath = "$dir/$file";
    if (is_dir($filePath)) continue;
    
    $content = file_get_contents($filePath);
    $size = strlen($content);
    
    // 1. app/Console/Commands/ServeCommand.php
    if (strpos($content, 'class ServeCommand') !== false) {
        restoreFile('app/Console/Commands/ServeCommand.php', $content, $file);
    }
    
    // 2. resources/views/installments/contract_print.blade.php
    if (strpos($content, 'កិច្ចសន្យាបង់រំលស់') !== false && strpos($content, 'str_pad($installment->id') !== false) {
        restoreFile('resources/views/installments/contract_print.blade.php', $content, $file);
    }
    
    // 3. public/save_qr_text.php
    if (strpos($content, 'qr_text.txt') !== false && strpos($content, 'file_put_contents') !== false) {
        restoreFile('public/save_qr_text.php', $content, $file);
    }
    
    // 4. test_khqr.php
    if (strpos($content, 'test_khqr') !== false || (strpos($content, 'KHQR') !== false && strpos($content, 'Bakong') !== false && $size < 10000 && strpos($content, '<?php') !== false)) {
        restoreFile('test_khqr.php', $content, $file);
    }
    
    // 5. decode_branch.php
    if (strpos($content, 'decode_branch') !== false || (strpos($content, 'branch') !== false && strpos($content, 'decode') !== false && $size < 5000 && strpos($content, '<?php') !== false)) {
        restoreFile('decode_branch.php', $content, $file);
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
    echo "SUCCESS: Restored $targetPath from blob $hash\n";
}

echo "=== RESTORATION COMPLETE ===\n";
echo "Total files restored: " . count($restored) . "\n";
print_r($restored);
