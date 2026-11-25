<?php
declare(strict_types=1);

$config = require dirname(__DIR__) . '/config/config.php';
date_default_timezone_set($config['timezone']);

require dirname(__DIR__) . '/app/Database.php';
require dirname(__DIR__) . '/app/Security.php';
require dirname(__DIR__) . '/app/Auth.php';
require dirname(__DIR__) . '/app/Audit.php';
require dirname(__DIR__) . '/app/View.php';
require dirname(__DIR__) . '/app/helpers.php';

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_name($config['session_name']);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();
Security::headers();

try {
    $db = Database::connection($config);
} catch (Throwable $e) {
    http_response_code(500);
    error_log($e->getMessage());
    exit('Database belum siap. Pastikan ekstensi pdo_sqlite aktif dan jalankan migrasi.');
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($path === '/login') {
    if ($method === 'POST') {
        Security::verifyCsrf();
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $attempts = (int) ($_SESSION['_login_attempts'] ?? 0);
        $lastAttempt = (int) ($_SESSION['_login_last'] ?? 0);
        if ($attempts >= 8 && time() - $lastAttempt < 900) {
            http_response_code(429);
            exit('Terlalu banyak percobaan login. Coba lagi 15 menit kemudian.');
        }

        if (Auth::login($db, $email, $password)) {
            unset($_SESSION['_login_attempts'], $_SESSION['_login_last']);
            $user = Auth::user($db);
            Audit::record($db, (int) $user['id'], 'login', 'user', (int) $user['id']);
            redirect('/dashboard');
        }

        $_SESSION['_login_attempts'] = $attempts + 1;
        $_SESSION['_login_last'] = time();
        flash('error', 'Email atau password tidak sesuai.');
        redirect('/login');
    }

    if (Auth::user($db)) {
        redirect('/dashboard');
    }
    View::render('login', ['title' => 'Masuk']);
    exit;
}

$user = Auth::user($db);
if (!$user) {
    redirect('/login');
}

if ($path === '/logout' && $method === 'POST') {
    Security::verifyCsrf();
    Audit::record($db, (int) $user['id'], 'logout', 'user', (int) $user['id']);
    Auth::logout();
    redirect('/login');
}

if ($path === '/' || $path === '/dashboard') {
    $stats = [
        'users' => (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'institutions' => (int) $db->query('SELECT COUNT(*) FROM institutions')->fetchColumn(),
        'programs' => (int) $db->query('SELECT COUNT(*) FROM study_programs')->fetchColumn(),
        'placements' => (int) $db->query('SELECT COUNT(*) FROM placements')->fetchColumn(),
    ];
    View::render('dashboard', ['title' => 'Dashboard', 'user' => $user, 'stats' => $stats]);
    exit;
}

if ($path === '/institutions') {
    Auth::requireRole($user, ['admin', 'management', 'auditor']);

    if ($method === 'POST') {
        Auth::requireRole($user, ['admin']);
        Security::verifyCsrf();
        $name = trim((string) ($_POST['name'] ?? ''));
        $type = trim((string) ($_POST['institution_type'] ?? 'Perguruan Tinggi'));
        $contact = trim((string) ($_POST['contact_name'] ?? ''));
        $email = trim((string) ($_POST['contact_email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));

        if (preg_match_all('/./us', $name) < 2 || preg_match_all('/./us', $name) > 160 || preg_match_all('/./us', $type) > 80 ||
            ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            flash('error', 'Data institusi tidak valid.');
            redirect('/institutions');
        }

        $stmt = $db->prepare(
            'INSERT INTO institutions (name,institution_type,contact_name,contact_email,phone,is_active,created_at,updated_at)
             VALUES (:name,:type,:contact,:email,:phone,1,:created,:updated)'
        );
        $now = gmdate('Y-m-d H:i:s');
        $stmt->execute([
            'name' => $name, 'type' => $type, 'contact' => $contact ?: null,
            'email' => $email ?: null, 'phone' => $phone ?: null,
            'created' => $now, 'updated' => $now,
        ]);
        $id = (int) $db->lastInsertId();
        Audit::record($db, (int) $user['id'], 'create', 'institution', $id);
        flash('success', 'Institusi berhasil ditambahkan.');
        redirect('/institutions');
    }

    $rows = $db->query('SELECT id,name,institution_type,contact_name,contact_email,phone,is_active FROM institutions ORDER BY name LIMIT 200')->fetchAll();
    View::render('institutions', ['title' => 'Institusi', 'user' => $user, 'rows' => $rows]);
    exit;
}

http_response_code(404);
View::render('error', ['title' => 'Tidak ditemukan', 'user' => $user, 'message' => 'Halaman tidak ditemukan.']);
