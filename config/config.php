<?php
declare(strict_types=1);

return [
    'app_name' => getenv('APP_NAME') ?: 'E-Dokumen PKL',
    'base_path' => dirname(__DIR__),
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Jakarta',
    'session_name' => 'edokumen_session',
    'db_path' => getenv('EDOKUMEN_DB_PATH') ?: dirname(__DIR__) . '/storage/edokumen.sqlite',
    'debug' => filter_var(getenv('APP_DEBUG') ?: '0', FILTER_VALIDATE_BOOL),
];
