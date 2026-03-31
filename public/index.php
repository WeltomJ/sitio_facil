<?php
declare(strict_types=1);

// Ponto de entrada único da aplicação (Front Controller)
define('ROOT_PATH', dirname(__DIR__));

$appConfig = require ROOT_PATH . '/config/app.php';
define('BASE_URL', rtrim($appConfig['url'], '/'));

date_default_timezone_set($appConfig['timezone']);

// Autoload manual — sem necessidade de Composer
spl_autoload_register(function (string $class): void {
    $path = ROOT_PATH . '/' . str_replace(['App\\', '\\'], ['app/', '/'], $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// Sessão segura
session_name($appConfig['session']['name']);
session_set_cookie_params([
    'lifetime' => $appConfig['session']['lifetime'],
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

// Determina o método HTTP e a URI limpa
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Remove o prefixo do BASE_URL do path
$basePath = parse_url($appConfig['url'], PHP_URL_PATH);
if ($basePath && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}
$uri = '/' . ltrim($uri, '/');

// Carrega e despacha as rotas
$router = new App\Core\Router();
require ROOT_PATH . '/routes/web.php';
$router->dispatch($method, $uri);
