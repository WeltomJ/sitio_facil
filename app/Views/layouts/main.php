<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= BASE_URL ?>">
    <meta name="theme-color" content="#2E7D32">
    <meta name="description" content="Sítio Fácil é o marketplace líder em aluguel de chácaras para finais de semana e férias. Encontre a chácara perfeita com piscina, churrasqueira e muito mais. Reserve online com segurança e facilidade.">
    <meta name="keywords" content="aluguel de chácaras, sítio para alugar, chácara com piscina, chácara com churrasqueira, aluguel de sítios, reserva de chácaras, locação de chácaras, chácaras para finais de semana, chácaras para férias">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/favicon.svg">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/images/favicon.svg">
    <title><?= htmlspecialchars($pageTitle ?? 'Sítio Fácil') ?></title>

    <!-- Anti-FOUC: aplica tema salvo antes de qualquer render -->
    <script>
        (function() {
            try {
                var t = localStorage.getItem('sf-theme');
                if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch (e) {}
        })();
    </script>

    <!-- Inter — tipografia moderna (Google Fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/vendor/popperjs/popper.min.js">
    <link rel="stylesheet" href="<?= BASE_URL ?>/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/app.css">

    <!-- Injeta BASE_URL como variável JS global -->
    <script src="<?= BASE_URL ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?= BASE_URL ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/vendor/jquery-validate/jquery.validate.min.js"></script>

    <script>
        var BASE_URL = '<?= rtrim(BASE_URL, '/') ?>';
    </script>
    <script src="<?= BASE_URL ?>/js/theme.js"></script>
    <script src="<?= BASE_URL ?>/js/app.js"></script>
    <script src="<?= BASE_URL ?>/js/utils.js"></script>
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
                                <?php if (!empty($_SESSION['foto_url'])): ?>
                                    <img src="<?= BASE_URL . htmlspecialchars($_SESSION['foto_url']) ?>"
                                         alt="<?= htmlspecialchars($_SESSION['nome'] ?? '') ?>"
                                         class="sf-avatar sf-avatar--photo">
                                <?php else: ?>
                                    <span class="sf-avatar sf-avatar--initial">
                                        <?= strtoupper(mb_substr($_SESSION['nome'] ?? 'U', 0, 1)) ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <!-- Cabeçalho com nome do usuário -->
                                <li class="px-3 py-2 d-flex align-items-center gap-2">
                                    <?php if (!empty($_SESSION['foto_url'])): ?>
                                        <img src="<?= BASE_URL . htmlspecialchars($_SESSION['foto_url']) ?>"
                                             alt="" class="sf-avatar sf-avatar--photo" style="width:36px;height:36px;">
                                    <?php else: ?>
                                        <span class="sf-avatar sf-avatar--initial" style="width:36px;height:36px;font-size:.9rem;">
                                            <?= strtoupper(mb_substr($_SESSION['nome'] ?? 'U', 0, 1)) ?>
                                        </span>
                                    <?php endif; ?>
                                    <div>
                                        <p class="fw-semibold mb-0 small"><?= htmlspecialchars($_SESSION['nome'] ?? '') ?></p>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>/perfil">
                                        <i class="fas fa-user-circle me-2 text-muted"></i> Meu Perfil
                                    </a>
                                </li>
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
                                <?php if (str_contains($_SESSION['perfil'] ?? '', 'CLIENTE')): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= BASE_URL ?>/cliente/cartoes">
                                            <i class="fas fa-credit-card me-2 text-muted"></i> Meus Cartões
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (str_contains($_SESSION['perfil'] ?? '', 'LOCADOR')): ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <h6 class="dropdown-header" style="font-size:.65rem; letter-spacing:.08em;">LOCADOR</h6>
                                    </li>
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
                                    <li>
                                        <a class="dropdown-item" href="<?= BASE_URL ?>/locador/perfil">
                                            <i class="fas fa-university me-2 text-muted"></i> Dados de Recebimento
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
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
                <script>
                    $(function () {
                        notification({ type: 'success', message: '<?= addslashes(htmlspecialchars($_SESSION['_flash']['success'])) ?>' });
                    });
                </script>
                <?php unset($_SESSION['_flash']['success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['_flash']['error'])): ?>
                <script>
                    $(function () {
                        notification({ type: 'error', message: '<?= addslashes(htmlspecialchars($_SESSION['_flash']['error'])) ?>' });
                    });
                </script>
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
                <div class="sf-footer-col">
                    <div class="sf-footer-col-title">Legal</div>
                    <a href="<?= BASE_URL ?>/termos">Termos de Uso</a>
                    <a href="<?= BASE_URL ?>/privacidade">Política de Privacidade</a>
                    <a href="<?= BASE_URL ?>/cancelamento">Política de Cancelamento</a>
                    <a href="https://www.consumidor.gov.br" target="_blank" rel="noopener">
                        consumidor.gov.br <i class="fas fa-external-link-alt ms-1" style="font-size:.6rem;"></i>
                    </a>
                </div>
            </div>
            <div class="sf-footer-bottom">
                <span class="sf-footer-brand">
                    <i class="fas fa-tree me-1" style="color:var(--sf-green-300);"></i>
                    Sítio Fácil
                </span>
                <span class="sf-footer-copy">
                    &copy; <?= date('Y') ?> Sítio Fácil &mdash; Marketplace de aluguel de chácaras &mdash;
                    <a href="<?= BASE_URL ?>/privacidade" style="color:inherit;opacity:.7;">Privacidade</a> &middot;
                    <a href="<?= BASE_URL ?>/termos" style="color:inherit;opacity:.7;">Termos</a>
                </span>
            </div>
        </div>
    </footer>
</body>

</html>