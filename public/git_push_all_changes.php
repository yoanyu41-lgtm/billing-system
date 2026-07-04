<?php
header('Content-Type: text/plain');

chdir(__DIR__ . '/..');

$commands = [
    'git add -A',
    'git commit -m "Standardize Khmer text, Riel symbols, custom fonts, and payment reminders in PDF templates and controller notifications"',
    'git push origin billing',
    'git checkout main',
    'git merge billing -m "Merge branch billing into main: Standardize Khmer text, Riel symbols, fonts, and reminders"',
    'git push origin main',
    'git checkout billing'
];

foreach ($commands as $cmd) {
    echo "=== Running: $cmd (in " . getcwd() . ") ===\n";
    $output = shell_exec($cmd . ' 2>&1');
    echo $output . "\n";
}
