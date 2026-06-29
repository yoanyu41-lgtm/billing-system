<?php
header('Content-Type: text/plain');

$commands = [
    'git status',
    'git add app/Console/Commands/ServeCommand.php decode_branch.php public/decode.html public/decode.php public/decode_latest.php public/decode_via_api.php public/git_status.php public/save_qr_text.php public/test_chrome.php public/test_crc.php',
    'git commit -m "Restore all files deleted by git clean -fd"',
    'git push'
];

foreach ($commands as $cmd) {
    echo "=== Running: $cmd ===\n";
    $output = shell_exec($cmd . ' 2>&1');
    echo $output . "\n";
}
