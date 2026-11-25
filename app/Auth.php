<?php
declare(strict_types=1);

final class Auth
{
    public static function user(PDO $db): ?array
    {
        $id = $_SESSION['user_id'] ?? null;
        if (!is_int($id) && !ctype_digit((string) $id)) {
            return null;
        }

        $stmt = $db->prepare(
            'SELECT id, name, email, role, is_active FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => (int) $id]);
        $user = $stmt->fetch();

        if (!$user || (int) $user['is_active'] !== 1) {
            self::logout();
            return null;
        }

        return $user;
    }

    public static function login(PDO $db, string $email, string $password): bool
    {
        $stmt = $db->prepare(
            'SELECT id, password_hash, is_active FROM users WHERE email = :email COLLATE NOCASE LIMIT 1'
        );
        $stmt->execute(['email' => trim($email)]);
        $user = $stmt->fetch();

        if (!$user || (int) $user['is_active'] !== 1 || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        unset($_SESSION['_csrf']);
        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => 'Lax',
            ]);
        }
        session_destroy();
    }

    public static function requireRole(array $user, array $roles): void
    {
        if (!in_array($user['role'], $roles, true)) {
            http_response_code(403);
            exit('Anda tidak memiliki akses ke halaman ini.');
        }
    }
}
