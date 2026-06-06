<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
foreach (App\Models\Product::whereNotNull('image')->get() as $p) {
    echo $p->id . ' ' . $p->code . ' ' . $p->image . PHP_EOL;
}
