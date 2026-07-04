<?php
header('Content-Type: text/plain');
chdir(__DIR__ . '/..');
echo shell_exec("git diff resources/views/invoices/pdf.blade.php 2>&1");
