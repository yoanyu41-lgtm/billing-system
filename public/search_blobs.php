<?php
$dir = ".git/lost-found/other";
if (!is_dir($dir)) {
    $dir = "../.git/lost-found/other";
}

if (!is_dir($dir)) {
    die("Lost found directory not found.\n");
}

$files = scandir($dir);
$count = 0;

echo "=== POTENTIAL CODE FILES FOUND ===\n\n";

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $filePath = "$dir/$file";
    $content = file_get_contents($filePath);
    $size = filesize($filePath);
    
    // Check if it's a PHP, HTML, or JSON file
    if (strpos($content, '<?php') !== false || strpos($content, '<div') !== false || strpos($content, 'class ServeCommand') !== false || strpos($content, '@extends') !== false) {
        $count++;
        
        $lines = explode("\n", $content);
        $previewLines = array_slice($lines, 0, 8); // show first 8 lines
        
        echo "[$count] Hash: $file | Size: $size bytes\n";
        echo "--------------------------------------------------\n";
        foreach ($previewLines as $i => $line) {
            echo "  " . ($i + 1) . ": " . trim($line) . "\n";
        }
        echo "--------------------------------------------------\n\n";
    }
}

echo "Total matching blobs: $count\n";
