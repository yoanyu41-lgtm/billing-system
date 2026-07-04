<?php
header('Content-Type: text/plain');
chdir(__DIR__ . '/..');
echo shell_exec("git status 2>&1");
