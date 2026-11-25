<?php
declare(strict_types=1);

final class Security
{
    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function verifyCsrf(): void
    {
        $submitted = $_POST['_csrf'] ?? '';
        $stored = $_SESSION['_csrf'] ?? '';

        if (!is_string($submitted) || !is_string($stored) || $stored === '' || !hash_equals($stored, $submitted)) {
            http_response_code(419);
            exit('Permintaan kedaluwarsa. Muat ulang halaman dan coba kembali.');
        }
    }

    public static function headers(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
    }
}
