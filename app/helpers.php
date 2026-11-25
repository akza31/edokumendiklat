<?php
declare(strict_types=1);

function e(?string $value): string
{
    return Security::escape($value);
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Security::csrfToken()) . '">';
}

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return is_string($value) ? $value : null;
}
