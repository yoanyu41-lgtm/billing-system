<?php

$files = [
    'set_test_merchant.php',
    'set_real_bakong.php',
    'restore_original.php',
    'test_generate_city_tech.php',
    'test_crc_original.php',
    'test_crc.php',
    'test_crc_math.php',
    'decode_wing_app_qr.php',
    'clear_cache.php',
    'set_final_wing_merchant.php',
    'set_bakong_city_teach.php',
    'set_wing_city_teach.php',
    'test_dynamic_12_qr.php',
    'test_dynamic_99_qr.php',
    'test_final_city_teach_qr.php',
    'result.txt'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        unlink($path);
        echo "Deleted: $file\n";
    }
}

echo "Cleanup done.\n";
