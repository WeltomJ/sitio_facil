<?php $pageTitle = 'Acesso negado — Sítio Fácil'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Sítio Fácil</title>
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/vendor/bootstrap/css/bootstrap.min.css">
</head>
<body class="min-vh-100 d-flex align-items-center bg-light">
<div class="container text-center">
    <p class="display-1 text-secondary"><i class="fas fa-lock"></i> 403</p>
    <h1 class="fw-bold">Acesso negado</h1>
    <p class="text-muted">Você não tem permissão para acessar este recurso.</p>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>/" class="btn btn-primary mt-4">
        Voltar ao início
    </a>
</div>
</body>
</html>
