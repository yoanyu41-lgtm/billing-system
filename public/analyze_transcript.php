<?php
$logFile = 'C:/Users/Yu/.gemini/antigravity-ide/brain/8cdf5737-a8f5-4220-b6dd-a4e3b3dd45d7/.system_generated/logs/transcript_full.jsonl';
if (!file_exists($logFile)) {
    die("Log file not found");
}

$handle = fopen($logFile, 'r');
if (!$handle) {
    die("Could not open log file");
}

$interestingFiles = [
    'contract_print.blade.php',
    'save_qr_text.php',
    'test_khqr.php',
    'ServeCommand.php',
    'decode_branch.php'
];

$writes = [];
$views = [];
$lineNum = 0;

while (($line = fgets($handle)) !== false) {
    $lineNum++;
    $data = json_decode($line, true);
    if (!$data) continue;
    
    // Check for tool calls (writes or views)
    if (isset($data['tool_calls']) && is_array($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $tool) {
            $name = $tool['name'] ?? '';
            $args = $tool['args'] ?? [];
            
            // Check write_to_file
            if ($name === 'write_to_file') {
                $targetFile = $args['TargetFile'] ?? '';
                $codeContent = $args['CodeContent'] ?? '';
                foreach ($interestingFiles as $inf) {
                    if (strpos($targetFile, $inf) !== false) {
                        $writes[$inf][] = [
                            'step' => $data['step_index'],
                            'type' => 'write_to_file',
                            'content' => $codeContent
                        ];
                    }
                }
            }
            
            // Check replace_file_content
            if ($name === 'replace_file_content' || $name === 'multi_replace_file_content') {
                $targetFile = $args['TargetFile'] ?? '';
                foreach ($interestingFiles as $inf) {
                    if (strpos($targetFile, $inf) !== false) {
                        $writes[$inf][] = [
                            'step' => $data['step_index'],
                            'type' => $name,
                            'args' => $args
                        ];
                    }
                }
            }
            
            // Check view_file calls
            if ($name === 'view_file') {
                $absPath = $args['AbsolutePath'] ?? '';
                foreach ($interestingFiles as $inf) {
                    if (strpos($absPath, $inf) !== false) {
                        $views[$inf][] = [
                            'step' => $data['step_index'],
                            'start' => $args['StartLine'] ?? null,
                            'end' => $args['EndLine'] ?? null
                        ];
                    }
                }
            }
        }
    }
    
    // Check if it is a view response containing file contents
    if (isset($data['type']) && $data['type'] === 'VIEW_FILE') {
        $content = $data['content'] ?? '';
        foreach ($interestingFiles as $inf) {
            if (strpos($content, $inf) !== false) {
                // This view file response contains code lines
                $views[$inf][] = [
                    'step' => $data['step_index'],
                    'type' => 'response',
                    'content' => $content
                ];
            }
        }
    }
}

fclose($handle);

// Output the summary
header('Content-Type: text/plain; charset=utf-8');
echo "=== TRANSCRIPT ANALYSIS ===\n";
foreach ($interestingFiles as $inf) {
    echo "\n----------------------------------------\n";
    echo "FILE: $inf\n";
    echo "----------------------------------------\n";
    echo "Writes count: " . (isset($writes[$inf]) ? count($writes[$inf]) : 0) . "\n";
    if (isset($writes[$inf])) {
        foreach ($writes[$inf] as $w) {
            echo "  - Step {$w['step']} (Type: {$w['type']}) - content size: " . strlen($w['content'] ?? json_encode($w['args'])) . " bytes\n";
        }
    }
    echo "Views/Responses count: " . (isset($views[$inf]) ? count($views[$inf]) : 0) . "\n";
    if (isset($views[$inf])) {
        foreach ($views[$inf] as $v) {
            if (isset($v['content'])) {
                echo "  - Step {$v['step']} (Response) - size: " . strlen($v['content']) . " bytes\n";
            } else {
                echo "  - Step {$v['step']} (Call) - lines {$v['start']} to {$v['end']}\n";
            }
        }
    }
}
