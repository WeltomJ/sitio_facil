<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $path = ROOT_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($path)) {
            throw new \RuntimeException("View [{$view}] não encontrada.");
        }

        // Captura o HTML da view e injeta no layout via $content
        ob_start();
        require $path;
        $content = ob_get_clean();

        require ROOT_PATH . '/app/Views/layouts/main.php';
    }

    protected function redirect(string $url): never
    {
        header("Location: {$url}");
        exit;
    }

    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect(BASE_URL . '/login');
        }
    }

    protected function requirePerfil(string $perfil): void
    {
        $this->requireAuth();
        $perfis = explode(',', $_SESSION['perfil'] ?? '');
        if (!in_array($perfil, $perfis, true)) {
            http_response_code(403);
            require ROOT_PATH . '/app/Views/errors/403.php';
            exit;
        }
    }

    protected function flashError(string $message): void
    {
        $_SESSION['_flash']['error'] = $message;
    }

    protected function flashSuccess(string $message): void
    {
        $_SESSION['_flash']['success'] = $message;
    }
}
