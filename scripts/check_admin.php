<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "DB default: " . config('database.default') . PHP_EOL;
try {
    $count = \App\Models\Admin::count();
    echo "Admin count: " . $count . PHP_EOL;
    $admin = \App\Models\Admin::first();
    if ($admin) {
        echo "Admin email: " . $admin->email . PHP_EOL;
        echo "Password hash: " . $admin->password . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error querying Admins: " . $e->getMessage() . PHP_EOL;
}
