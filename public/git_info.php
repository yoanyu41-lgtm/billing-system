<?php
header('Content-Type: text/plain');
echo "=== GIT INFO ===\n\n";

echo "--- git status ---\n";
echo shell_exec('git status 2>&1') . "\n";

echo "--- git log -n 10 --oneline ---\n";
echo shell_exec('git log -n 10 --oneline 2>&1') . "\n";

echo "--- git branch -a ---\n";
echo shell_exec('git branch -a 2>&1') . "\n";

echo "--- git reflog -n 20 ---\n";
echo shell_exec('git reflog -n 20 2>&1') . "\n";
