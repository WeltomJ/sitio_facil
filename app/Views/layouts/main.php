<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= BASE_URL ?>">
    <meta name="theme-color" content="#2E7D32">
    <title><?= htmlspecialchars($pageTitle ?? 'Sítio Fácil') ?></title>

    <!-- Anti-FOUC: aplica tema salvo antes de qualquer render -->
    <script>
        (function(){
            try {
                var t = localStorage.getItem('sf-theme');
                if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch(e){}
        })();
    </script>

    <!-- Inter — tipografia moderna (Google Fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/app.css">

    <!-- Injeta BASE_URL como variável JS global -->
    <script>var BASE_URL = '<?= rtrim(BASE_URL, '/') ?>';</script>
</head>
<body>

<nav class="navbar navbar-expand-lg sf-navbar fixed-top" role="navigation" aria-label="navegação principal">
    <div class="container">
        <a class="navbar-brand fw-bold fs-5" href="<?= BASE_URL ?>/">
            <i class="fas fa-tree me-2"></i> Sítio Fácil
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/chacaras">
                        <i class="fas fa-search me-1"></i> Buscar Chácaras
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav align-items-lg-center gap-1">

                <!-- Toggle Claro/Escuro -->
                <li class="nav-item">
                    <button id="btn-theme-toggle"
                            class="btn sf-btn-theme"
                            title="Mudar para modo escuro"
                            aria-label="Mudar para modo escuro">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= BASE_URL ?>/notificacoes" title="Notificações">
                            <i class="fas fa-bell"></i>
                            <?php if (($naoLidas ?? 0) > 0): ?>
                                <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size:.55rem;">
                                    <?= (int) $naoLidas ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle sf-user-pill" href="#"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bars me-1" style="font-size:.85rem;"></i>
                            <span class="sf-avatar"><i class="fas fa-user" style="font-size:.75rem;"></i></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/dashboard">
                                    <i class="fas fa-tachometer-alt me-2 text-muted"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/minhas-reservas">
                                    <i class="fas fa-calendar-check me-2 text-muted"></i> Minhas Reservas
                                </a>
                            </li>
                            <?php if (str_contains($_SESSION['perfil'] ?? '', 'LOCADOR')): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header" style="font-size:.65rem; letter-spacing:.08em;">LOCADOR</h6></li>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>/locador/chacaras">
                                        <i class="fas fa-home me-2 text-muted"></i> Minhas Chácaras
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>/locador/reservas">
                                        <i class="fas fa-list me-2 text-muted"></i> Reservas Recebidas
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>/locador/chacaras/nova">
                                        <i class="fas fa-plus me-2 text-muted"></i> Cadastrar Chácara
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i> Sair
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary btn-sm rounded-pill px-3" href="<?= BASE_URL ?>/login">Entrar</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm rounded-pill px-3" href="<?= BASE_URL ?>/cadastro">Cadastrar-se</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="sf-main">
    <div class="container">

        <?php if (!empty($_SESSION['_flash']['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_SESSION['_flash']['success']) ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
            <?php unset($_SESSION['_flash']['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['_flash']['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($_SESSION['_flash']['error']) ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
            <?php unset($_SESSION['_flash']['error']); ?>
        <?php endif; ?>

        <?= $content ?>

    </div>
</main>

<footer class="sf-footer">
    <div class="container">
        <div class="sf-footer-top">
            <div class="sf-footer-col">
                <div class="sf-footer-col-title">Explorar</div>
                <a href="<?= BASE_URL ?>/chacaras">Buscar Chácaras</a>
                <a href="<?= BASE_URL ?>/chacaras?tipo=piscina">Com Piscina</a>
                <a href="<?= BASE_URL ?>/chacaras?tipo=churrasqueira">Com Churrasqueira</a>
                <a href="<?= BASE_URL ?>/chacaras?tipo=lago">Beira de Lago</a>
            </div>
            <div class="sf-footer-col">
                <div class="sf-footer-col-title">Seja Anfitrião</div>
                <a href="<?= BASE_URL ?>/cadastro">Anunciar Minha Chácara</a>
                <a href="<?= BASE_URL ?>/dashboard">Painel do Locador</a>
                <a href="<?= BASE_URL ?>/locador/chacaras/nova">Cadastrar Propriedade</a>
            </div>
            <div class="sf-footer-col">
                <div class="sf-footer-col-title">Sítio Fácil</div>
                <a href="<?= BASE_URL ?>/login">Fazer Login</a>
                <a href="<?= BASE_URL ?>/cadastro">Criar Conta</a>
                <a href="<?= BASE_URL ?>/notificacoes">Notificações</a>
            </div>
        </div>
        <div class="sf-footer-bottom">
            <span class="sf-footer-brand">
                <i class="fas fa-tree me-1" style="color:var(--sf-green-300);"></i>
                Sítio Fácil
            </span>
            <span class="sf-footer-copy">
                &copy; <?= date('Y') ?> Sítio Fácil &mdash; Marketplace de aluguel de chácaras
            </span>
        </div>
    </div>
</footer>

<script src="<?= BASE_URL ?>/vendor/jquery/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/js/theme.js"></script>
<script src="<?= BASE_URL ?>/js/app.js"></script>
</body>
</html>
