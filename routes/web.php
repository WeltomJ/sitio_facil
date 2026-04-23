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
$router->post('/locador/chacaras/{id}/fotos/{fotoId}/excluir',   ['ChacaraController', 'excluirFoto']);
$router->post('/locador/chacaras/{id}/fotos/{fotoId}/principal', ['ChacaraController', 'definirFotoPrincipal']);

$router->get('/locador/reservas',                      ['ReservaController', 'indexLocador']);
$router->post('/locador/reservas/{id}/confirmar',      ['ReservaController', 'confirmar']);
$router->post('/locador/reservas/{id}/recusar',        ['ReservaController', 'recusar']);

// Perfil do locador — dados bancários
$router->get('/locador/perfil',  ['PerfilController', 'locador']);
$router->post('/locador/perfil', ['PerfilController', 'salvarLocador']);

// ── Área do Cliente ──────────────────────────────────────────────────────────
$router->get('/chacaras/{id}/checkout',        ['ReservaController', 'checkoutForm']);
$router->get('/chacaras/{id}/datas-ocupadas',  ['ReservaController', 'datasOcupadas']);
$router->post('/chacaras/{id}/reservar',       ['ReservaController', 'store']);
$router->get('/reservas/{id}/confirmacao',     ['ReservaController', 'confirmacao']);
$router->get('/reservas/{id}/status-pagamento',['ReservaController', 'statusPagamento']);
$router->get('/minhas-reservas',               ['ReservaController', 'historico']);
$router->post('/reservas/{id}/cancelar',       ['ReservaController', 'cancelar']);
$router->post('/reservas/{id}/avaliar',        ['AvaliacaoController', 'store']);

// Perfil do usuário (qualquer perfil)
$router->get('/perfil',         ['PerfilController', 'usuario']);
$router->post('/perfil',        ['PerfilController', 'salvarPerfil']);
$router->post('/perfil/foto',   ['PerfilController', 'uploadFoto']);
$router->post('/perfil/foto/remover', ['PerfilController', 'removerFoto']);

// Cartões salvos do cliente
$router->get('/cliente/cartoes',                     ['PerfilController', 'cartoes']);
$router->post('/cliente/cartoes',                    ['PerfilController', 'adicionarCartao']);
$router->post('/cliente/cartoes/{id}/remover',       ['PerfilController', 'removerCartao']);

// ── Notificações ─────────────────────────────────────────────────────────────
$router->get('/notificacoes',             ['NotificacaoController', 'index']);
$router->post('/notificacoes/{id}/ler',   ['NotificacaoController', 'marcarLida']);

// ── Webhook Asaas ────────────────────────────────────────────────────────────
$router->post('/webhook/asaas', ['WebhookController', 'handle']);

// ── Páginas Legais (públicas) ─────────────────────────────────────────────────
$router->get('/termos',       ['LegalController', 'termos']);
$router->get('/privacidade',  ['LegalController', 'privacidade']);
$router->get('/cancelamento', ['LegalController', 'cancelamento']);
