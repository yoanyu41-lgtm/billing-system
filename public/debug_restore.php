<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== DEBUG BLOBS ===\n";

$dir = "../.git/lost-found/other";
if (!is_dir($dir)) {
    $dir = ".git/lost-found/other";
}

if (!is_dir($dir)) {
    die("Lost found directory not found at $dir");
}

$files = scandir($dir);
echo "Directory path: " . realpath($dir) . "\n";
echo "Total files in directory: " . count($files) . "\n\n";

$count = 0;
foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    $count++;
    
    $filePath = "$dir/$file";
    $content = file_get_contents($filePath);
    $size = filesize($filePath);
    $preview = substr(str_replace(["\r", "\n"], " ", $content), 0, 150);
    
    echo "[$count] Hash: $file | Size: $size bytes\n";
    echo "    Preview: $preview\n\n";
    
    if ($count >= 40) {
        echo "... showing first 40 files ...\n";
        break;
    }
}
