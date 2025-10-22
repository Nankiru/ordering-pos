<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = $argv[1] ?? 'heng@gmail.com';
$admin = \App\Models\Admin::where('email', $email)->first();
if ($admin) {
    echo "Found admin: id={$admin->id} email={$admin->email} name={$admin->name}\n";
} else {
    echo "Admin with email {$email} not found\n";
}
