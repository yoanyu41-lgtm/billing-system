<?php
header('Content-Type: text/plain');
echo "=== GIT STATUS ===\n";
echo shell_exec('git status 2>&1') . "\n";
echo "=== GIT LOG ===\n";
echo shell_exec('git log -n 3 2>&1') . "\n";
