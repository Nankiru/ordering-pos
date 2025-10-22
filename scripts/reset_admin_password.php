<?php

// Usage: php scripts/reset_admin_password.php "email@example.com" "newpassword"
// This script updates the admins table password field with a bcrypt hash.

declare(strict_types=1);

$email = $argv[1] ?? 'dimhourt@gmail.com';
$plain = $argv[2] ?? 'heng1234';

$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    fwrite(STDERR, "SQLite database not found: {$dbPath}\n");
    exit(1);
}

$hash = password_hash($plain, PASSWORD_BCRYPT);
if ($hash === false) {
    fwrite(STDERR, "Failed to hash password.\n");
    exit(1);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec('CREATE TABLE IF NOT EXISTS admins (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT UNIQUE, password TEXT, created_at TEXT, updated_at TEXT)');

    // Ensure an admin row exists for the email
    $stmt = $pdo->prepare('SELECT id FROM admins WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $row = $stmt->fetch();

    if ($row) {
        $upd = $pdo->prepare('UPDATE admins SET password = :password, updated_at = :ts WHERE id = :id');
        $upd->execute([
            ':password' => $hash,
            ':ts' => date('Y-m-d H:i:s'),
            ':id' => $row['id'],
        ]);
        echo json_encode(['reset' => true, 'email' => $email, 'updated' => true]) . "\n";
    } else {
        $ins = $pdo->prepare('INSERT INTO admins (name, email, password, created_at, updated_at) VALUES (:name, :email, :password, :ts, :ts)');
        $ins->execute([
            ':name' => 'Admin',
            ':email' => $email,
            ':password' => $hash,
            ':ts' => date('Y-m-d H:i:s'),
        ]);
        echo json_encode(['reset' => true, 'email' => $email, 'created' => true]) . "\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");
    exit(1);
}


