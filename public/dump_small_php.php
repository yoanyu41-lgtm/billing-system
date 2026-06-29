<?php
$dir = ".git/lost-found/other";
if (!is_dir($dir)) {
    $dir = "../.git/lost-found/other";
}

if (!is_dir($dir)) {
    die("Lost found directory not found.");
}

$files = scandir($dir);
$output = "=== DUMPING ALL SMALL PHP FILES ===\n\n";
$count = 0;

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $filePath = "$dir/$file";
    $content = file_get_contents($filePath);
    $size = filesize($filePath);
    
    if ($size < 10000 && strpos($content, '<?php') !== false) {
        $count++;
        $output .= "==================================================\n";
        $output .= "BLOB HASH: $file | SIZE: $size bytes\n";
        $output .= "==================================================\n";
        $output .= $content . "\n";
        $output .= "--------------------------------------------------\n\n\n";
    }
}

file_put_contents(__DIR__ . '/all_small_php.txt', $output);
echo "Written $count files successfully";
