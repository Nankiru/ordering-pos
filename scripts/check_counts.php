<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'Categories: ' . \App\Models\Category::count() . PHP_EOL;
echo 'Items: ' . \App\Models\Item::count() . PHP_EOL;
echo 'Users: ' . \App\Models\User::count() . PHP_EOL;
