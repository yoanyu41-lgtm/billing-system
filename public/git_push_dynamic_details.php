<?php
header('Content-Type: text/plain');

chdir(__DIR__ . '/..');

$commands = [
    'git add app/Http/Controllers/Api/TelegramController.php',
    'git commit -m "Parse and display bank details dynamically from KHQR payload in Telegram bot"',
    'git push origin billing',
    'git checkout main',
    'git merge billing -m "Merge branch billing into main: Dynamic bank info in Telegram"',
    'git push origin main',
    'git checkout billing'
];

foreach ($commands as $cmd) {
    echo "=== Running: $cmd (in " . getcwd() . ") ===\n";
    $output = shell_exec($cmd . ' 2>&1');
    echo $output . "\n";
}
