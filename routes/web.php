<?php
/** @var \App\Core\Router $router */

// ── Autenticação ────────────────────────────────────────────────────────────
$router->get('/login',     ['AuthController', 'showLogin']);
$router->post('/login',    ['AuthController', 'login']);
$router->get('/cadastro',  ['AuthController', 'showCadastro']);
$router->post('/cadastro', ['AuthController', 'cadastro']);
$router->get('/logout',    ['AuthController', 'logout']);

// ── Home / Busca ─────────────────────────────────────────────────────────────
$router->get('/',          ['ChacaraController', 'index']);
$router->get('/chacaras',  ['ChacaraController', 'index']);
$router->get('/chacaras/{id}', ['ChacaraController', 'show']);

// ── Dashboard ────────────────────────────────────────────────────────────────
$router->get('/dashboard', ['DashboardController', 'index']);

// ── Área do Locador ──────────────────────────────────────────────────────────
$router->get('/locador/chacaras',              ['ChacaraController', 'minhasChacaras']);
$router->get('/locador/chacaras/nova',         ['ChacaraController', 'create']);
$router->post('/locador/chacaras',             ['ChacaraController', 'store']);
$router->get('/locador/chacaras/{id}/editar',  ['ChacaraController', 'edit']);
$router->post('/locador/chacaras/{id}',        ['ChacaraController', 'update']);

$router->get('/locador/reservas',                      ['ReservaController', 'indexLocador']);
$router->post('/locador/reservas/{id}/confirmar',      ['ReservaController', 'confirmar']);
$router->post('/locador/reservas/{id}/recusar',        ['ReservaController', 'recusar']);

// ── Área do Cliente ──────────────────────────────────────────────────────────
$router->post('/chacaras/{id}/reservar', ['ReservaController', 'store']);
$router->get('/minhas-reservas',         ['ReservaController', 'historico']);
$router->post('/reservas/{id}/cancelar', ['ReservaController', 'cancelar']);
$router->post('/reservas/{id}/avaliar',  ['AvaliacaoController', 'store']);

// ── Notificações ─────────────────────────────────────────────────────────────
$router->get('/notificacoes',              ['NotificacaoController', 'index']);
$router->post('/notificacoes/{id}/ler',   ['NotificacaoController', 'marcarLida']);
