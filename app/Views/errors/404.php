<?php $pageTitle = 'Página não encontrada — Sítio Fácil'; ?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Sítio Fácil</title>
    <script>
        (function() {
            try {
                var t = localStorage.getItem('sf-theme');
                if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch(e) {}
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/css/app.css">
</head>
<body class="min-vh-100 d-flex align-items-center">
<div class="container py-5 text-center">
    <i class="fas fa-map-signs fa-4x mb-4" style="color:var(--sf-green-300);opacity:.6;"></i>
    <p class="fw-bold mb-1" style="font-size:5rem;line-height:1;color:var(--sf-text-strong);">404</p>
    <h1 class="h3 fw-bold mb-2">Página não encontrada</h1>
    <p class="text-muted mb-4">O recurso que você procura não existe ou foi removido.</p>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>/" class="btn btn-primary px-5">
        <i class="fas fa-home me-2"></i>Voltar ao início
    </a>
</div>
</body>
</html>
