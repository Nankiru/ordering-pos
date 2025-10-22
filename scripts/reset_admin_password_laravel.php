<?php
// Usage: php scripts/reset_admin_password_laravel.php "email@example.com" "newpassword"
declare(strict_types=1);

$email = $argv[1] ?? 'dimhourt@gmail.com';
$plain = $argv[2] ?? 'heng1234';

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var Illuminate\Contracts\Config\Repository $config */
$config = app('config');
$conn = $config->get('database.default');

$hash = password_hash($plain, PASSWORD_BCRYPT);
if ($hash === false) {
    fwrite(STDERR, "Hash failed\n");
    exit(1);
}

try {
    /** @var Illuminate\Database\ConnectionInterface $db */
    $db = Illuminate\Support\Facades\DB::connection($conn);
    // Ensure admins table exists on this connection
    $exists = $db->getSchemaBuilder()->hasTable('admins');
    if (!$exists) {
        fwrite(STDERR, "admins table not found on connection '{$conn}'\n");
        exit(2);
    }

    $row = $db->table('admins')->where('email', $email)->first();
    $now = date('Y-m-d H:i:s');
    if ($row) {
        $db->table('admins')->where('id', $row->id)->update(['password' => $hash, 'updated_at' => $now]);
        $updated = true; $created = false;
    } else {
        $db->table('admins')->insert(['name' => 'Admin', 'email' => $email, 'password' => $hash, 'created_at' => $now, 'updated_at' => $now]);
        $updated = false; $created = true;
    }
    echo json_encode([
        'connection' => $conn,
        'email' => $email,
        'updated' => $updated,
        'created' => $created,
    ]) . "\n";
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}


