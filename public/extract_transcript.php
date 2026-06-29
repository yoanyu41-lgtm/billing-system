<?php
$logFile = 'C:/Users/Yu/.gemini/antigravity-ide/brain/8cdf5737-a8f5-4220-b6dd-a4e3b3dd45d7/.system_generated/logs/transcript_full.jsonl';
if (!file_exists($logFile)) {
    die("Log file not found at $logFile");
}

$handle = fopen($logFile, 'r');
if (!$handle) {
    die("Could not open log file.");
}

$output = "=== EXTRACTING FROM TRANSCRIPT ===\n\n";
$lineNum = 0;
while (($line = fgets($handle)) !== false) {
    $lineNum++;
    $data = json_decode($line, true);
    if (!$data) continue;
    
    if (isset($data['type']) && ($data['type'] === 'VIEW_FILE' || $data['type'] === 'PLANNER_RESPONSE')) {
        $content = $data['content'] ?? '';
        if (empty($content)) {
            if (isset($data['tool_calls'])) {
                $content = json_encode($data['tool_calls']);
            }
        }
        
        if (strpos($content, 'contract_print') !== false) {
            $output .= "Step index: {$data['step_index']} | Type: {$data['type']} | Line: $lineNum\n";
            $output .= "Content Length: " . strlen($content) . " bytes\n";
            $output .= "--------------------------------------------------\n";
            $output .= substr($content, 0, 5000) . "\n";
            $output .= "==================================================\n\n";
        }
    }
}
fclose($handle);

file_put_contents(__DIR__ . '/extract_transcript_output.txt', $output);
echo "Written successfully";
