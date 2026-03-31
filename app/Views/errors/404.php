<?php $pageTitle = 'Página não encontrada — Sítio Fácil'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Sítio Fácil</title>
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/vendor/bootstrap/css/bootstrap.min.css">
</head>
<body class="min-vh-100 d-flex align-items-center bg-light">
<div class="container text-center">
    <p class="display-1 text-secondary">404</p>
    <h1 class="fw-bold">Página não encontrada</h1>
    <p class="text-muted">O recurso que você procura não existe ou foi removido.</p>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>/" class="btn btn-primary mt-4">
        Voltar ao início
    </a>
</div>
</body>
</html>
