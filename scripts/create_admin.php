<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

$name = $argv[1] ?? 'heng';
$email = $argv[2] ?? 'heng@gmail.com';
$password = $argv[3] ?? 'heng1122';

$admin = Admin::updateOrCreate(
    ['email' => $email],
    ['name' => $name, 'password' => Hash::make($password), 'role' => 'admin']
);

if ($admin) {
    echo "Admin created/updated:\n";
    echo "  id: " . $admin->id . "\n";
    echo "  name: " . $admin->name . "\n";
    echo "  email: " . $admin->email . "\n";
    echo "  password_matches: " . (Hash::check($password, $admin->password) ? 'true' : 'false') . "\n";
} else {
    echo "Failed to create admin\n";
}
