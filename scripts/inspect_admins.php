<?php
declare(strict_types=1);

$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    fwrite(STDERR, "DB not found: {$dbPath}\n");
    exit(1);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $rows = $pdo->query('SELECT id,name,email,password FROM admins')->fetchAll();
    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'id' => $r['id'],
            'name' => $r['name'],
            'email' => $r['email'],
            'password_prefix' => substr((string)$r['password'], 0, 10) . '...',
            'verify_heng1234' => password_verify('heng1234', (string)$r['password']),
        ];
    }
    echo json_encode($out, JSON_PRETTY_PRINT) . "\n";
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}


