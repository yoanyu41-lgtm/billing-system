<?php
$logFile = 'C:/Users/Yu/.gemini/antigravity-ide/brain/8cdf5737-a8f5-4220-b6dd-a4e3b3dd45d7/.system_generated/logs/transcript_full.jsonl';
if (!file_exists($logFile)) {
    die("Log file not found");
}

$handle = fopen($logFile, 'r');
if (!$handle) {
    die("Could not open log file");
}

$output = "=== SERVE COMMAND SEARCH ===\n\n";
$lineNum = 0;
while (($line = fgets($handle)) !== false) {
    $lineNum++;
    $data = json_decode($line, true);
    if (!$data) continue;
    
    $content = $data['content'] ?? '';
    if (empty($content) && isset($data['tool_calls'])) {
        $content = json_encode($data['tool_calls']);
    }
    
    if (strpos($content, 'ServeCommand') !== false) {
        $output .= "Step index: {$data['step_index']} | Type: {$data['type']} | Line: $lineNum\n";
        $output .= "--------------------------------------------------\n";
        $output .= substr($content, 0, 10000) . "\n";
        $output .= "==================================================\n\n";
    }
}
fclose($handle);

file_put_contents(__DIR__ . '/search_transcript_serve_output.txt', $output);
echo "Written successfully\n";
