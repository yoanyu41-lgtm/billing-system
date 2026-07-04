<?php
header('Content-Type: text/plain; charset=utf-8');

$dir = ".git/lost-found/other";
if (!is_dir($dir)) {
    $dir = "../.git/lost-found/other";
}

if (!is_dir($dir)) {
    die("Lost found directory not found.\n");
}

$files = scandir($dir);
$count = 0;

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $filePath = "$dir/$file";
    $content = file_get_contents($filePath);
    
    if (stripos($content, 'khqr') !== false || stripos($content, 'bakong') !== false) {
        $count++;
        echo "==================================================\n";
        echo "MATCH #$count | Hash: $file | Size: " . strlen($content) . " bytes\n";
        echo "==================================================\n";
        echo $content;
        echo "\n\n";
    }
}

echo "Total matching blobs: $count\n";
