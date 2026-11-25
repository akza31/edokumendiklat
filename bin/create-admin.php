<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$config = require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/app/Database.php';

$email = $argv[1] ?? '';
$password = $argv[2] ?? '';
$name = $argv[3] ?? 'Administrator KOMKORDIK';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
    fwrite(STDERR, "Penggunaan: php bin/create-admin.php email password-min-12-karakter [nama]\n");
    exit(1);
}

$db = Database::connection($config);
$db->exec(file_get_contents(dirname(__DIR__) . '/database/schema.sql'));

$stmt = $db->prepare(
    'INSERT INTO users (name,email,password_hash,role,is_active,created_at,updated_at)
     VALUES (:name,:email,:password_hash,:role,1,:created_at,:updated_at)'
);
try {
    $now = gmdate('Y-m-d H:i:s');
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'admin',
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    fwrite(STDOUT, "Admin berhasil dibuat.\n");
} catch (PDOException $e) {
    fwrite(STDERR, "Gagal membuat admin. Email mungkin sudah digunakan.\n");
    exit(1);
}
