<?php
declare(strict_types=1);

final class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__) . '/views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            exit('Template tidak ditemukan.');
        }
        require dirname(__DIR__) . '/views/layout.php';
    }
}
