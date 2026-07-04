<?php
header('Content-Type: text/plain');

chdir(__DIR__ . '/..');

echo "=== git show ===\n";
echo shell_exec("git show --name-status 0b51e3f08e7a0433cc4176b19698924af83de35c 2>&1") . "\n";
echo shell_exec("git show --name-status e7619bbbf90fe944d512cee71633c13dbb4e9958 2>&1") . "\n";
