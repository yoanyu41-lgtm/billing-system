<?php
// Temporary migration script - DELETE THIS FILE AFTER USE!
// Access: http://127.0.0.1:8080/run_migration.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Step 1: Drop the foreign key constraint
    DB::statement('ALTER TABLE `purchases` DROP FOREIGN KEY `purchases_supplier_id_foreign`');
    echo "✅ Step 1: Foreign key constraint dropped.<br>";
} catch (\Exception $e) {
    echo "⚠️ Step 1 skipped (FK may not exist): " . $e->getMessage() . "<br>";
}

try {
    // Step 2: Make the column nullable
    DB::statement('ALTER TABLE `purchases` MODIFY COLUMN `supplier_id` BIGINT UNSIGNED NULL');
    echo "✅ Step 2: Column supplier_id is now NULLABLE.<br>";
} catch (\Exception $e) {
    echo "❌ Step 2 failed: " . $e->getMessage() . "<br>";
}

try {
    // Step 3: Re-add foreign key with nullOnDelete
    DB::statement('ALTER TABLE `purchases` ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE SET NULL');
    echo "✅ Step 3: Foreign key re-added with ON DELETE SET NULL.<br>";
} catch (\Exception $e) {
    echo "❌ Step 3 failed: " . $e->getMessage() . "<br>";
}

echo "<br><strong>✅ Done! Now delete this file: public/run_migration.php</strong>";
