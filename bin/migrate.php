<?php
declare(strict_types=1);

$config = require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/app/Database.php';

$db = Database::connection($config);
$sql = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
if ($sql === false) {
    fwrite(STDERR, "Schema database tidak ditemukan.\n");
    exit(1);
}
$db->exec($sql);
fwrite(STDOUT, "Database SQLite siap: {$config['db_path']}\n");
