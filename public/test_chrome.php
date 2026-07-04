<?php
$chromePaths = [
    'C:\Program Files\Google\Chrome\Application\chrome.exe',
    'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
    getenv('USERPROFILE') . '\AppData\Local\Google\Chrome\Application\chrome.exe',
];

$foundPath = null;
foreach ($chromePaths as $path) {
    if (file_exists($path)) {
        $foundPath = $path;
        break;
    }
}

echo "<h1>Chrome Finder Test</h1>";
if ($foundPath) {
    echo "<p style='color:green;'>Found Chrome at: <b>$foundPath</b></p>";
    
    // Test headless printing
    $testHtml = "<html><body style='font-family:sans-serif;'><h1>សួស្តីពិភពលោក (Khmer Unicode Test)</h1><p>This is a test printed using headless Chrome!</p></body></html>";
    $htmlFile = __DIR__ . '/chrome_test.html';
    $pdfFile = __DIR__ . '/chrome_test.pdf';
    
    file_put_contents($htmlFile, $testHtml);
    
    $command = '"' . $foundPath . '" --headless --disable-gpu --print-to-pdf="' . $pdfFile . '" "' . $htmlFile . '" 2>&1';
    echo "<p>Running command: <code>$command</code></p>";
    
    $output = shell_exec($command);
    echo "<pre>Output:\n" . htmlspecialchars($output) . "</pre>";
    
    if (file_exists($pdfFile)) {
        echo "<p style='color:green;'><b>Success! PDF file generated successfully.</b> Size: " . filesize($pdfFile) . " bytes</p>";
        echo "<p><a href='/chrome_test.pdf' target='_blank'>Click here to view/download the generated PDF</a></p>";
    } else {
        echo "<p style='color:red;'><b>Error: PDF file was not generated.</b></p>";
    }
} else {
    echo "<p style='color:red;'>Google Chrome not found in standard paths.</p>";
}
?>
