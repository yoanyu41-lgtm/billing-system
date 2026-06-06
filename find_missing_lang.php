<?php
$lang = require('lang/en/app.php');
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
$missing = [];
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() == 'php') {
        $content = file_get_contents($file->getPathname());
        preg_match_all("/__\('app\.([a-zA-Z0-9_]+)'\)/", $content, $matches);
        foreach ($matches[1] as $match) {
            if (!isset($lang[$match])) {
                $missing[$match] = true;
            }
        }
    }
}
print_r(array_keys($missing));
